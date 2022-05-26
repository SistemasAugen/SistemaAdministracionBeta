<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
    </head>
    <style media="screen">
    /*
        Naranja - #FF2B00
        Gris obscuro - #333333
        Gris claro - #E6E6E6
    */

        /* @font-face {
            font-family: 'Proxima Nova Light';
            src: url('/public/fonts/Proxima-Nova-Light.otf') format("opentype");
        }
        @font-face {
            font-family: 'Proxima Semibold';
            src: url('/public/fonts/Proxima-Nova-Sbold.otf') format("opentype");
        } */

        body {
            background: #E6E6E6;
            padding-top: 35px;
            padding-bottom: 35px;
            font-family: 'Helvetica' !important;
        }

        .container {
            background: #FFFFFF;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            padding: 55px;
            text-align: center;
            color: #333333;
        }

        .container img {
            /* width: 50%; */
            /* display: block; */
        }

        .title {
            text-align: left;
            margin-top: 35px;
        }

        .title h1 {
            font-weight: bold;
            /* font-family: 'Proxima Semibold' !important; */
            color: #FF2B00;
            font-size: 65px;
            display: inline;
        }

        .title h3 {
            font-size: 25px;
            display: inline;
        }

        .message {
            padding: 45px;
            padding-bottom: 75px;
            border-bottom: 1px solid #333333;
            font-size: 20px;
            text-align: left;
            font-weight: lighter;
        }

        .details {
            text-align: left;
        }

        .details h3 {
            font-weight: bold;
            /* font-family: 'Proxima Semibold' !important; */
            color: #FF2B00;
            font-size: 25px;
        }

        .details .content {
            padding: 35px 65px;
            padding-top: 0px !important;
            font-size: 16px;
        }

        .promotion {
            background: #FF2B00;
            color: #fff;
            padding-top: 10px;
            padding-bottom: 10px;
            width: 200px !important;
            margin: 0 auto;
            border-radius: 15px;
        }

        .promotion p {
            margin: 0px !important;
            line-height: 25px;
            font-size: 16px;
        }

        .social a {
            text-decoration: none !important;
        }

        .social img {
            width: 50px !important;
            margin-top: 25px;
            margin-bottom: 75px;
        }
    </style>
    <body>
        <div class="container">
            <img src="https://augensistema.com/public/images/augen-labs.png" alt="">
            <br/>
            <div class="title">
                <h1>Hola, </h1><h3>{{ mb_strtolower($order->client->name) }}</h3>
            </div>
            <div class="message">
                Te informamos que tu orden {{ $order->rx }} ha sido procesada con éxito y pronto estará en tu negocio
            </div>
            <div class="details">
                <h3>Tu orden:</h3>
                <div class="content">
                    @if (isset($order->product['id']))
                        <p>{{ $order->product['name']}}, {{ $order->product['subcategory_name'] }}, {{ $order->product['type_name'] }}</p>
                    @endif
                    @if (count($order->extras) > 0)
                        <p>{{ implode(', ', array_map(function($e) {
                            return $e->name;
                        }, $order->extras)) }}</p>
                    @endif
                </div>
            </div>
            <div class="promotion">
                <p>Descubre nuestra</p>
                <p><b>Promoción del mes</b></p>
            </div>
            <div class="social">
                <a href="#"><img src="https://augensistema.com/public/images/icons/fblogo.png" alt=""> </a>
                <a href="#"><img src="https://augensistema.com/public/images/icons/lnlogo.png" alt=""> </a>
                <a href="https://www.instagram.com/augenlabs/"><img src="https://augensistema.com/public/images/icons/inlogo.png" alt=""> </a>
            </div>
        </div>
    </body>
</html>
