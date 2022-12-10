# API For Trade4FastPay Project 




__Note: *The Api Was Versioned by Storing the Controllers in a Route folder and 
adding a url prefix of V1*.__

## Routes available : 

__Note: *Base = localhost,http://127.0.0.1:8000 or any base route 
created by your local machine or remote host.*__

### Routes for Unauthorized Users.
* Register User : Base/api/V1/register
* Send mail verification : Base/api/V1/send-verify-mail/{email} 
* Forgot password : Base/api/V1/forgot-password/{email} 
* Reset password : Base/api/V1/reset-password/{token} 

### Routes for Authorized Users only
* Logout : Base/api/V1/logout
### Trade Routes
__Note: *Trades can only be made by an authorized user (user with a token) 
and the trades made are only available to that particular user that has 
been authorized after login*__
* trade(GET method)  : Base/api/V1/trade (fetch all the trade for the authorized user)
* trade (POST method) : Base/api/V1/trade (creates a trade for the particular authorized user)
* trade-show (GET method) : Base/api/V1/trade/{id} (shows a trade with an id of the value specified where {id} is. user for the particular authorized user)
* trade-delete (DELETE method) : Base/api/V1/trade/{id} (delete the trade made )
api/V1/trade/1

### Presumed Work Flow for registration and login 
* When a user comes then sign in. 
* after sign in a verification link is sent which they click to verify their mail 
* Then are told to login 
* After login they get authorized (by being assigned a token)
* With the token the can now access the trade functionality 
* As only authorized users should be able to trade coin(or so) 

### Presumed Work Flow for password forget and reset 
* Enter the mail (my back end code check if the mail actually belongs to a user)
* If yes for above then the mail is sent to the mail inserted in the form field 
* finally the password is changed 

### JSON Response Structure not confirmed yet 