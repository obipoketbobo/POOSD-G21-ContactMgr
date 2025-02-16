const urlBase = 'http://contactmanager.group21contactmanager.site/API';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
//	var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

	if (!login && !password) {
        document.getElementById("loginResult").innerText = "Please enter your username and password.";
        return;
    }
    if (!login) {
        document.getElementById("loginResult").innerText = "Please enter your username.";
        return;
    }
    if (!password) {
        document.getElementById("loginResult").innerText = "Please enter your password.";
        return;
    }

	let tmp = {email:login,password:password};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "contacts.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
//		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function doRegister() {
    let firstName = document.getElementById("firstName").value;
	let lastName = document.getElementById("lastName").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    
    document.getElementById("registerResult").innerHTML = "";
	if (!firstName && !lastName && !email && !password) {
        document.getElementById('registerResult').innerText = 'Please fill out all fields.';
        return;
    }
    if (!firstName) {
        document.getElementById('registerResult').innerText = 'Please enter your first name.';
        return;
    }
    if (!lastName) {
        document.getElementById('registerResult').innerText = 'Please enter your last name.';
        return;
    }
    if (!email) {
        document.getElementById('registerResult').innerText = 'Please enter your email.';
        return;
    }
    if (!password) {
        document.getElementById('registerResult').innerText = 'Please enter a password.';
        return;
    }
    
    let tmp = { firstName: firstName, lastName: lastName, email: email, password: password };
    let jsonPayload = JSON.stringify(tmp);
    
    let url = urlBase + '/registration.' + extension;
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    
    try {
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                
                if (jsonObject.error) {
                    document.getElementById("registerResult").innerHTML = jsonObject.error;
                    return;
                }
                
                userId = jsonObject.id;
                firstName = jsonObject.firstName;
				lastName = jsonObject.lastName
                email = jsonObject.email;
                
                saveCookie();
                window.location.href = "index.html";
            }
        };
        xhr.send(jsonPayload);
    } catch (err) {
        document.getElementById("registerResult").innerHTML = err.message;
    }
}



