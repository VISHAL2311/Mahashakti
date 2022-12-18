<!DOCTYPE html>
<html>

<head>
    <title>Oops! 404 The requested page not found</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        /* - - - - - - - - - - - - * Update Date: 19 11 2019 - - - - - - - - - - - - - */
        
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    width: 100%;
}

.notfound_01 {
    position: relative;
    z-index: 1;
}

.notfound_01 .error_image-1 {
    height: calc(100vh - 60px);
    width: 100%;
    background-image: url('../../../../assets/images/Rectangle 3718.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    min-height: 100vh;
    width: 100%;
}

.notfound_01 .error_message-1 { 
    padding: 0;
    max-width: 690px;
    margin-left: 129px;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
}

.notfound_01 .error_message-1 .error-title-1 {
    color: white;
    margin-bottom: 30px;
    font-size: 70px;
}

.notfound_01 .error_message-1 .error_para-1 {
    margin-bottom: 30px;
    color: white;
    font-size: 23px;
}

.notfound_01 .error_message-1 .ac-wht {
    z-index: 1;
    border-color: #fff;
    color: #fff;
    margin-top: 18px;
}

.ac-btn {
    color: #0080d9;
    border-color: #0080d9;
    font-size: 16px;
    font-weight: 400;
    font-family: Bolt;
    overflow: hidden;
    z-index: 10;
    position: relative;
    line-height: 120%;
    padding: 15px 20px;
    margin: 0;
    border: 2px solid;
    text-transform: uppercase;
    cursor: pointer;
    display: inline-block;
    white-space: nowrap;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .8px;
    -webkit-transition: all .5s ease-in-out;
    -khtml-transition: all .5s ease-in-out;
    -moz-transition: all .5s ease-in-out;
    -ms-transition: all .5s ease-in-out;
    -o-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out;
    -webkit-border-radius: 0;
    -khtml-border-radius: 0;
    -moz-border-radius: 0;
    -ms-border-radius: 0;
    -o-border-radius: 0;
    border-radius: 0;
    -webkit-box-shadow: none;
    -khtml-box-shadow: none;
    -moz-box-shadow: none;
    -ms-box-shadow: none;
    -o-box-shadow: none;
    box-shadow: none;
    text-decoration: none;
}

.ac-btn::after {
    content: "";
    position: absolute;
    display: inline-block;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    color: #fff;
    background-color: #0080d9;
    border-bottom-left-radius: 100%;
    border-bottom-right-radius: 100%;
    -webkit-transition: all .5s ease-in-out;
    -khtml-transition: all .5s ease-in-out;
    -moz-transition: all .5s ease-in-out;
    -ms-transition: all .5s ease-in-out;
    -o-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out;
    -webkit-transform: translateY(-100%);
    -khtml-transform: translateY(-100%);
    -moz-transform: translateY(-100%);
    -ms-transform: translateY(-100%);
    -o-transform: translateY(-100%);
    transform: translateY(-100%);
    z-index: -1;
    opacity: 0;
}
.ac-btn:hover::after{
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    -webkit-transform: translateY(0);
    -khtml-transform: translateY(0);
    -moz-transform: translateY(0);
    -ms-transform: translateY(0);
    -o-transform: translateY(0);
    transform: translateY(0);
    opacity: 1;
}

@media(max-width:991px) {
    .notfound_01 .error_image-1 {
        background-position: center;
    }

    .notfound_01 .error_message-1 { 
        margin-left: 59px;
    } 
} 
        
       
    </style>
</head>

<body>
    <section class="notfound_01">
        <div class="container-fluid p-0">
            <div class="error_image-1">
                <div class="error_message-1">
                    <h2 class="error-title-1">ERROR 404 <br />NOT FOUND</h2>
                    <p class="error_para-1 pb-1">You may have mistyped the URL. Or the page has been removed. Actually, there is nothing to see here...</p>
                    <p class="error_para-1">Click on the links below to do something, Thanks!</p>
                    <a class="ac-btn ac-wht" href="{{ url('/powerpanel') }}" title="Back to Home"><span class="text">Back to Home</span><span class="line"></span></a>
                </div>
            </div>
        </div>
    </section>
</body>

</html>