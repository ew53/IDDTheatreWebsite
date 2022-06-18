/*jslint white:true */
/*global angular */
var app = angular.module("showingApp", ["ngRoute", "angular-md5"]);

//Set chrome's local storage for saving values
app.factory('$storage', function($window) {
    return {
        get: function(key) {
            var value = $window.localStorage[key];
            return value ? JSON.parse(value) : null;
        },
        set: function(key, value) {
            $window.localStorage[key] = JSON.stringify(value);
        },
        remove: function(key) {
            $window.localStorage.removeItem(key);
        }
    }
});

app.config(function($routeProvider) {

    //Route for purchasing ticket process
    $routeProvider
        .when("/", {
            templateUrl: "showingOverview.html",
            controller: "showingCtrl"
        }).
    when("/start", {
        templateUrl: "login.html",
        controller: "showingCtrl"
    }).
    when("/MovieDetails/:id", {
        templateUrl: "showingDetail.html",
        controller: "showingCtrl"
    }).
    when("/OnShowTheatres/:id", {
        templateUrl: "showingBUYTheatre.html",
        controller: "showingCtrl"
    }).
    when("/OnShowTime/:id", {
        templateUrl: "showingBUYTime.html",
        controller: "showingCtrl"
    }).
    when("/OnShowSeat/:id", {
        templateUrl: "showingBUYSeat.html",
        controller: "showingCtrl"
    })
});

//Home Controller
app.controller("topmovieCtrl", function($scope, $http, $window) {

    $http({
        method: 'get',
        url: 'asset/php/movieSql.php'
    }).then(function success(response) {
        $scope.movieData = response.data.movies;
        $scope.hero1 = $scope.movieData[0];
        $scope.hero2 = $scope.movieData[1];
        $scope.hero3 = $scope.movieData[2];

    });
});

//Purchasing Ticket Controller
app.controller("showingCtrl", function($scope, $http, $routeParams, $rootScope, $location, $storage) {

    $scope.$storage = $storage;

    //Get movie data from overview page
    $http({
        method: 'get',
        url: 'asset/php/movieSql.php'
    }).then(function success(response) {
        $rootScope.movieData = response.data.movies;
        $rootScope.index = $rootScope.movieData.findIndex(x => x.id === $routeParams.id);

    });

    //Get theatre data of movie showtime
    $http({
        method: 'post',
        url: 'asset/php/onShowTheatre.php',
        data: {
            movieID: $routeParams.id,
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function success(response) {
        $rootScope.theatreData = response.data.ontheatre;
        $scope.theatreindex = $rootScope.theatreData.findIndex(y => y.motid === $routeParams.id);
    })

    //Get time data of movie showtime
    $http({
        method: 'post',
        url: 'asset/php/onShowTime.php',
        data: {
            motID: $routeParams.id,
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function success(response) {
        $scope.timeData = response.data.time;
        $scope.timeindex = $rootScope.theatreData.findIndex(y => y.sid === $routeParams.id);

    })

    //Get seat data of movie hall
    $http({
        method: 'post',
        url: 'asset/php/onShowSeat.php',
        data: {
            showtimeID: $routeParams.id,
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function success(response) {
        $scope.seatData = response.data.seat;

    })

    //But ticket and insert ticket data to database
    $scope.buyTicket = function(id) {

        $http({
            method: 'get',
            url: 'asset/php/showTicketID.php'
        }).then(function success(response) {
            $scope.ticketIDData = response.data.ticket;
            // if ($scope.ticketIDData.length = 0) {
            //     $scope.getTicketID = $rootScope.ticketID;
            // } else {
            //     $scope.getTicketID = $scope.ticketIDData.length;
            // }
            $scope.getTicketID = $scope.ticketIDData.length;
            $scope.seatIDNow = id;
            $storage.set('seatid', $scope.seatIDNow)

            $http({
                method: 'post',
                url: 'asset/php/newTicket.php',
                data: {
                    ticketID: $scope.getTicketID,
                    seatID: $scope.seatIDNow,
                    showtimeID: $routeParams.id,
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function success() {

            })
        });
    }

});

//Booking Ticket Controller
app.controller("buyTicketCtrl", function($scope, $http, $rootScope, $routeParams, $storage) {

    $scope.$storage = $storage;

    //Show ticket data for new ID
    $http({
        method: 'get',
        url: 'asset/php/showTicketID.php'
    }).then(function success(response) {
        $scope.ticketIDData = response.data.ticket;
        $scope.getTicketID = ($scope.ticketIDData.length - 1);

        //Get ticket data
        $http({
            method: 'post',
            url: 'asset/php/showTicket.php',
            data: {
                currentTicketID: $scope.getTicketID,
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function success(response) {
            $scope.ticketData = response.data.ticket;
            $storage.set('ticketid', $scope.ticketData[0].ticketid)
        })
    });

    //Insert new booking for the member
    $scope.updateTable = function() {
        $http({
            method: 'get',
            url: 'asset/php/showBooking.php',
        }).then(function success(response) {
            $scope.bookingData = response.data.booking;
            $storage.set('bookingid', $scope.bookingData.length);

            $http({
                method: 'post',
                url: 'asset/php/newBooking.php',
                data: {
                    bookingID: $storage.get('bookingid'),
                    ticketID: $storage.get('ticketid'),
                    memberID: $storage.get('user'),
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function success() {

            })
            alert("Booking confirmed");
        })
    }

    //Add this function while seat database is fully created
    // $scope.deleteSeat = function() {
    //     $http({
    //         method: 'post',
    //         url: 'updateSeat.php',
    //         data: {
    //             bookingID: $storage.get('bookingid'),
    //         },
    //         headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //     }).then(function success() {

    //     })
    // }

    //Delete ticket when member decide stop purchase
    $scope.deleteTicket = function() {
        $http({
            method: 'post',
            url: 'asset/php/deleteTicket.php',
            data: {
                ticketID: $storage.get('ticketid'),
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function success() {})
    }
});

//Login Controller
app.controller("startLoginCtrl", function($scope, $http, $window, $storage, md5) {

    $scope.$storage = $storage;

    //Login function to check the username and password
    $scope.login = function(username, password) {
        $http({
            method: 'get',
            url: 'asset/php/showMember.php'
        }).then(function success(response) {
            $scope.memberData = response.data.member;

            // $scope.wrongPassword = $scope.memberData[0].username;

            for (i = 0; i < $scope.memberData.length; i++) {
                if (username == $scope.memberData[i].username && md5.createHash(password || '') == $scope.memberData[i].password) {
                    $storage.set('user', $scope.memberData[i].id);
                    $storage.set('userfirstname', $scope.memberData[i].firstname);

                    window.location = "home.html";
                    break;
                } else {
                    $scope.wrongPassword = "Incorrect username or password";
                }
            }
        })
    }
})

//Register Controller
app.controller("startRegisterCtrl", function($scope, $http) {

    //Insert new member to database
    $scope.register = function(regusername, regpass, regfirstn, reglastn, regemail) {

        if (regusername == "" && regpass == "") {
            if (regfirstn == "" && reglastn == "") {
                alert("Please enter member's information");
            }
        } else {
            $http({
                method: 'get',
                url: 'asset/php/showMember.php'
            }).then(function success(response) {
                $scope.memberDataReg = response.data.member;
                for (i = 0; i < $scope.memberDataReg.length; i++) {
                    if (regusername == $scope.memberDataReg[i].username) {
                        $scope.message = "Username exist."
                        break;
                    } else {
                        $http({
                            method: 'post',
                            url: 'asset/php/newMember.php',
                            data: {
                                memberID: $scope.memberDataReg.length,
                                musername: regusername,
                                mpassword: regpass,
                                mfirstname: regfirstn,
                                mlastname: reglastn,
                                memail: regemail,
                                // memberID: 55,
                                // musername: "hello",
                                // mpassword: "password",
                                // mfirstname: "first",
                                // mlastname: "last",
                                // memail: "dsemail",
                            },
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        }).then(function success() {

                        })
                        alert("Account Registered");
                        window.location = "login.html";
                        break;
                    }
                }
            })
        }
    };
})

//Card Controller
app.controller("cardCtrl", function($scope, $http, $storage) {

    //Get saved cards of the member
    $http({
        method: 'post',
        url: 'asset/php/showMemberCard.php',
        data: {
            memberID: $storage.get('user'),
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function success(response) {
        $scope.cardDataMember = response.data.card;
    });

    //Insert new card to database while purchasing
    $scope.addCard = function(cardno, cvvno, expmonth, expyear, cardtype) {
        $scope.mastercardurl = "https://i.ibb.co/wBbVt8c/maestro.png";
        $scope.visaurl = "https://i.ibb.co/ZXV3Wc6/visa.png"
        if ($scope.cardtype == "MasterCard") {
            $scope.cardurl = $scope.mastercardurl;
        } else {
            $scope.cardurl = $scope.visaurl;
        }

        $http({
            method: 'get',
            url: 'asset/php/showCard.php',
        }).then(function success(response) {
            $scope.cardData = response.data.card;

            $http({
                method: 'post',
                url: 'asset/php/newCard.php',
                data: {
                    cardID: $scope.cardData.length,
                    memberID: $storage.get('user'),
                    cardNo: $scope.cardno,
                    cvvNo: $scope.cvvno,
                    expMonth: $scope.expmonth,
                    expYear: $scope.expyear,
                    cardType: $scope.cardtype,
                    cardUrl: $scope.cardurl,
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function success(response) {
                // $scope.mycardData = response.data.card;
            })
            alert("New Card Added");
            window.location = "ticketConfirm.html"
        })
    }

    //Insert new card to database at home
    $scope.addNewCard = function(cardno, cvvno, expmonth, expyear, cardtype) {
        $scope.mastercardurl = "https://i.ibb.co/wBbVt8c/maestro.png";
        $scope.visaurl = "https://i.ibb.co/ZXV3Wc6/visa.png"
        if ($scope.cardtype == "MasterCard") {
            $scope.cardurl = $scope.mastercardurl;
        } else {
            $scope.cardurl = $scope.visaurl;
        }

        $http({
            method: 'get',
            url: 'asset/php/showCard.php',
        }).then(function success(response) {
            $scope.cardData = response.data.card;

            $http({
                method: 'post',
                url: 'asset/php/newCard.php',
                data: {
                    cardID: $scope.cardData.length,
                    memberID: $storage.get('user'),
                    cardNo: $scope.cardno,
                    cvvNo: $scope.cvvno,
                    expMonth: $scope.expmonth,
                    expYear: $scope.expyear,
                    cardType: $scope.cardtype,
                    cardUrl: $scope.cardurl,
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function success(response) {
                // $scope.mycardData = response.data.card;
            })
            alert("New Card Added");
            window.location = "showingCard.html"
        })
    }

    //Delete card data from database
    $scope.deleteCard = function(id) {

        if (confirm("Delete the card?") == true) {
            $http({
                method: 'post',
                url: 'asset/php/deleteCard.php',
                data: {
                    cardID: id,
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function success(response) {

            })
            alert("Card Deleted");
            window.location.reload();
        } else {

        }
    }
})

//Booking Data Controller
app.controller("myTicketsCtrl", function($scope, $http, $storage) {

    //Get booking data of the member
    $http({
        method: 'post',
        url: 'asset/php/showMyBooking.php',
        data: {
            memberID: $storage.get('user'),
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function success(response) {
        $scope.myBookingData = response.data.mybooking;
    })
})

//Profile Controller
app.controller("profileCtrl", function($scope, $http, $storage, md5) {

    //Get member information from database
    $http({
        method: 'post',
        url: 'asset/php/showMemberProf.php',
        data: {
            memberID: $storage.get('user'),
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function success(response) {
        $scope.profileData = response.data.member;
        $scope.viewFirstName = $scope.profileData[0].firstname;
        $scope.viewLastName = $scope.profileData[0].lastname;
        $scope.viewUserName = $scope.profileData[0].username;
        $scope.viewEmail = $scope.profileData[0].email;
    })

    //Edit Profile Navigation
    $scope.editProf = function() {
        window.location = "profileCustomizeEdit.html"
    }

    //Update member table in database
    $scope.updateProf = function(firstname, lastname, email) {
        $http({
            method: 'post',
            url: 'asset/php/updateMemberProf.php',
            data: {
                memberID: $storage.get('user'),
                mFirstname: firstname,
                mLastname: lastname,
                mEmail: email,
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function success(response) {})
        alert("Profile Information Updated");
        window.location = "profileCustomizeView.html";
    };

    //Check password in database
    $scope.nextChangePass = function() {
        $scope.temPass = md5.createHash($scope.changePass || '');

        $http({
            method: 'post',
            url: 'asset/php/showMemberProf.php',
            data: {
                memberID: $storage.get('user'),
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function success(response) {
            $scope.profileDataPass = response.data.member;
            $scope.passwordPass = $scope.profileDataPass[0].password;

            if ($scope.passwordPass == $scope.temPass) {
                window.location = "changePass.html";
            } else {
                $scope.passMessage = "Password Not Match."
            }
        })
    }

    //Update password in database
    $scope.updatePass = function(password) {
        $http({
            method: 'post',
            url: 'asset/php/updateMemberPass.php',
            data: {
                memberID: $storage.get('user'),
                mPassword: md5.createHash(password || ''),
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function success(response) {})
        alert("Password Changed");
        window.location = "home.html";
    }
})