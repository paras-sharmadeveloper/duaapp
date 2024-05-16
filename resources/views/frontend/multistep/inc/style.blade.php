<style>
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    li {
        color: #ffffff;
    }

    body {
        background-color: rgb(29, 29, 29);
        font-family: Karla, sans-serif,
    }

    ul#progress-bar li {
        font-size: 17px;
    }

    .select2-container .select2-selection--single {
        height: 38px
    }

    .main-content .wizard-form .progressbar-list::before {
        content: " ";
        background-color: #9b9b9b;
        border: 10px solid #fff;
        border-radius: 50%;
        display: block;
        width: 30px;
        height: 30px;
        margin: 9px auto;
        box-shadow: 1px 1px 3px #606060;
        transition: none
    }

    .main-content .wizard-form .progressbar-list::after {
        content: "";
        background-color: #9b9b9b;
        padding: 0;
        position: absolute;
        top: 14px;
        left: -50%;
        width: 100%;
        height: 2px;
        margin: 9px auto;
        z-index: -1;
        transition: .8s
    }

    .main-content .wizard-form .progressbar-list.active::after {
        background-color: #763cb0
    }

    .statement-notes {
        height: 300px;
        overflow: auto;
    }

    .main-content .wizard-form .progressbar-list:first-child::after {
        content: none
    }

    .main-content .wizard-form .progressbar-list.active::before {
        font-family: "Font Awesome 5 free";
        content: "\f00c";
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        padding: 6px;
        background-color: #763cb0;
        border: 1px solid #763cb0;
        box-shadow: 0 0 0 7.5px rgb(118 60 176 / 11%)
    }

    .progressbar-list {
        color: #6f787d
    }

    .active {
        color: #000
    }

    .card img {
        width: 40px;
        margin: auto
    }

    .card {
        border: 3px solid rgb(145 145 145);
        cursor: pointer
    }

    .active-card {
        color: #763cb0;
        font-weight: 700;
        border: 6px solid #15d92b
    }

    .form-check-input:focus {
        box-shadow: none
    }

    .bg-color-info {
        background-color: #00d69f
    }

    .border-color {
        border-color: #ececec
    }

    .btn {
        padding: 16px 30px
    }

    .back-to-wizard {
        transform: translate(-50%, -139%) !important
    }

    .bg-success-color {
        background-color: #87d185
    }

    .bg-success-color:focus {
        box-shadow: 0 0 0 .25rem rgb(55 197 20 / 25%)
    }

    .row.justify-content-center.form-business.sloting-main .sloting-inner {
        max-height: 500px;
        height: 500px;
        overflow: overlay
    }

    div#slot-listing h1 {
        width: 100%
    }

    button.btn:hover {
        color: #fff !important;
        background-color: grey
    }

    .btn.back {
        color: #000 !important;
        background-color: grey;
        font-size: 18px;
    }

    .btn.back:hover {
        color: #fff !important;
    }

    .card-title {
        padding: 10px 0 8px;
        font-size: 24px;
        font-weight: 500;
        color: #012970;
        font-family: Poppins, sans-serif
    }

    .danger,
    .success {
        text-align: center;
        font-size: 16px
    }

    .card-body {
        padding: 0 17px 0 20px
    }

    #selfie-image,
    video#video {
        height: 250px;
        width: 300px
    }

    div#captured-image {
        margin-bottom: 15px
    }



    .success {
        color: green;
        font-weight: 900
    }

    .danger,
    .error {
        color: red;
        font-weight: bold
    }

    div#error {
        margin: 10px 0
    }

    @keyframes spin {
        0% {
            transform: rotate(0)
        }

        100% {
            transform: rotate(360deg)
        }
    }

    @media (max-width:767px) {
        #booknowStart {
            margin-top: 10% !important;
        }

        div#type-listing,
        #country-listing,
        #city-listing,
        #date-listing {
            max-height: 340px;
            overflow-x: scroll;
            height: 210px;
        }

        .row.justify-content-center.form-business.sloting-main .sloting-inner {
            height: 200px !important;
            max-height: 330px !important;
            overflow: overlay;
        }


        div#mobile-number label {
            font-size: 12px !important;
        }

        #startBooking {
            width: 100% !important;
        }

        .container-fluid {
            padding: 0px !important;
        }

        .head label {
            font-size: 15px !important;
            color: #fff !important;
        }

        div#loader {
            text-align: center !important;
        }

        .head {
            display: inherit !important;
            margin-top: 10px;
        }

        span.select2.select2-container.select2-container--default {
            width: 100% !important;
            flex: auto !important
        }

        .col {
            flex-shrink: 0 !important;
            flex: auto
        }

        .row.justify-content-center.form-business.sloting-main .sloting-inner {
            max-height: 375px
        }

        .selfie {
            text-align: center
        }

        .p-4 {
            padding: .5rem !important
        }

        .card {
            margin-bottom: 20px
        }

        .logoo img {
            height: 50px;
            width: 50px
        }

        .mt-4 {
            margin-top: .5rem !important
        }

        .error.country_code {
            font-size: 14px;
            bottom: -20px
        }

        .row .loader-img {
            margin: 17px !important
        }

        .thripist-section img {
            height: 100% !important;
            width: 100% !important;
        }

        .col-lg-6 {
            flex: 0 0 auto;
            width: 50% !important;
        }

        .otp-btn {
            text-align: center;
            margin: 12px 0px;
        }

        div#opt-form-confirm {
            text-align: center;
        }

        .cusmhtn {
            font-size: 12px;
        }

        /* .card.text-center.h-60.shadow-sm.thripist-section img {
                                max-height: 180px !important;
                            } */
        .col-xs-6.col-sm-4.col-md-4.col-lg-3 {
            width: 50% !important;
        }

        button#sendOtp {
            margin-top: 30px;
            float: none !important;
        }

    }

    .btn.next,
    #startBooking,
    .language-selection {
        background: #f9d20a !important;
        color: #000 !important;
        font-size: 18px;

    }

    .language-selection {
        width: 90%
    }

    @media (min-width:1024px) {
        .row.justify-content-center.form-business.sloting-main .sloting-inner {
            max-height: 380px
        }

        .error.country_code {
            bottom: -35px
        }

        /* .thripist-section img {

                            max-height: 264px !important;
                        } */


    }

    @media (min-width: 768px) and (max-width: 1024px) {
        .head label {
            font-size: 20px !important;
        }
    }


    figcaption {
        font-size: 10px
    }

    .thripist-section img {
        height: 100%;
        width: 100%;
        /* max-height: 300px;  */
    }

    .loader-img {
        height: 64px;
        width: 64px !important
    }

    #progressBar .w-25 {
        width: 14% !important
    }

    .row .loader-img {
        margin: auto
    }

    /* .col-lg-6 {
                                flex: 0 0 auto;
                                width: 20%;
                            } */
    .select2-container {
        width: 100%;
    }

    .select2-container {
        width: 100% !important;
    }

    /* css loader start  */


    .row .no-data {
        width: 100% !important;
        text-align: center;
        font-size: 26px;
    }

    .btn-cst {
        padding: 6px 10px;
    }

    .invalid-slot {
        border: 2px solid red;
    }


    .action-wrapper {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .action-wrapper p {
        margin: 0;
    }

    .action-wrapper select {
        padding: 5px;
        margin-left: 10px;
    }

    .wrapper {
        padding: 25px 35px;
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
        background: #fff;
        box-shadow: 0px 3px 10px 3px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }

    .wrapper ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        padding-left: 0;
        margin-top: 4.7px;
        margin-bottom: 0;
        list-style-type: none;
    }

    .wrapper ul li {
        position: relative;
        margin-top: 10px;
        font-size: 12px;
        flex-basis: 0;
        flex-grow: 1;
        max-width: 100%;
        text-align: center;
        color: #7f8995;
    }

    .wrapper ul li.d-none {
        display: none;
    }

    .wrapper ul li:last-child:after {
        display: none;
    }

    .wrapper ul li:before {
        content: "";
        position: absolute;
        top: -20px;
        left: 47%;
        z-index: 1;
        height: 8px;
        width: 8px;
        background: #000000;
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
    }

    .wrapper ul li:after {
        content: "";
        position: absolute;
        top: -17px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #FFEB3B;
    }

    .wrapper ul li.active~li:before {
        background: #dde2e5;
    }

    .wrapper ul li.active~li:after {
        background: rgba(221, 226, 229, 0.4);
    }

    .wrapper ul li.active:before {
        background-color: #0c0c0c;
        box-shadow: 0 0 0 3px rgba(25, 143, 209, 0.2);
    }

    .wrapper ul li.active:after {
        background: rgba(221, 226, 229, 0.4);
    }

    .head {
        display: flex;
        justify-content: space-between;
        color: white;
    }

    .head label {
        font-size: 26px;
        font-weight: 700;
    }

    .select2-results__options li {
        color: #000 !important;
    }

    #sendOtp label {
        color: #fff;
    }

    label.form-check-label {
        color: #fff;
    }

    .checkSlot img {
        position: absolute;
        top: 5px;
        right: 4px;
        height: 28px !important;
    }

    label {
        color: white;
    }

    /* button#sendOtp {
                    margin-top: 30px;
                    float: right;
                } */
    div#slot-information-user {
        padding: 10px;
        display: flex;
        justify-content: space-between;
        margin: 30px 0;
    }

    #slot-information-user select.change-timezone.form-control,
    #slot-information-user .select2-container {
        width: 30% !important;
        z-index: 99999999;
    }

    #slot-information-user .select2-container--default .select2-selection--single .select2-selection__rendered {

        line-height: 50px !important;
    }

    #slot-information-user .select2-container--default .select2-selection--single .select2-selection__arrow {

        top: 12px !important;
    }

    #slot-information-user .select2-container .select2-selection--single {

        height: 50px !important;
    }

    /* .box {
                 border: 1px solid #CCC;
                 padding: 40px 25px;
                 background: #FFF;
                 max-width: 400px;
                 position: relative;
                 border-radius: 3px;
                    margin: 30px auto;
                } */
    .box.ofh {
        overflow: hidden;
    }

    /* Ribbon 1 */
    .top-cross-ribbon {
        background: #090909;
        padding: 7px 50px;
        color: #FFF;
        position: absolute;
        top: 0;
        right: -50px;
        transform: rotate(45deg);
        border: 1px dashed #FFF;
        box-shadow: 0 0 0 3px #090909;
        margin: 5px;
    }

    /* Ribbon 2*/
    .arrow-ribbon {
        background: #090909;
        color: #FFF;
        padding: 4px 4px;
        position: absolute;
        top: 0px;
        right: -1px;
    }

    .arrow-ribbon:before {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        content: "";
        left: -12px;
        border-top: 15px solid transparent;
        border-right: 12px solid #090909;
        border-bottom: 15px solid transparent;
        width: 0;
    }

    /* Ribbon 3 */
    .bottom-ribbon {
        background: #090909;
        color: #FFF;
        padding: 7px 50px;
        position: absolute;
        bottom: 10px;
        right: -1px;
        border-radius: 20px 0 0 20px;
    }

    .bottom-ribbon:after {
        position: absolute;
        right: -25px;
        top: -18px;
        bottom: 0;
        z-index: 9999;
        content: "";
        border-bottom: 43px solid #090909;
        border-left: 38px solid transparent;
        border-right: 20px solid transparent;
        width: 42px;
        z-index: -1;
    }

    /*Ribbon 4 */
    .half-circle-ribbon {
        background: #090909;
        color: #FFF;
        height: 60px;
        width: 60px;
        text-align: right;
        padding-top: 10px;
        padding-right: 10px;
        position: absolute;
        top: -1px;
        right: -1px;
        flex-direction: row;
        border-radius: 0 0 0 100%;
        border: 1px dashed #FFF;
        box-shadow: 0 0 0 3px #EA4335;
    }

    /* Ribbon 5 */
    .cross-shadow-ribbon {
        position: absolute;
        background: #090909;
        top: -15px;
        padding: 10px;
        margin-left: 15px;
        color: #FFF;
        border-radius: 0 0 2px 2px;
    }

    .cross-shadow-ribbon:before {
        content: "";
        position: absolute;
        left: -15px;
        right: 0;
        top: 0;
        bottom: 0;
        width: 0;
        height: 0;
        border-bottom: 15px solid #090909;
        border-left: 15px solid transparent;
    }

    /* Ribbon 6 */
    .cover-ribbon {
        height: 115px;
        width: 115px;
        position: absolute;
        right: -8px;
        top: -8px;
        overflow: hidden;
    }

    .cover-ribbon .cover-ribbon-inside {
        background: #090909;
        color: #FFF;
        transform: rotate(45deg);
        position: absolute;
        right: -35px;
        top: 15px;
        padding: 10px;
        min-width: 127px;
        text-align: center;
    }

    .cover-ribbon .cover-ribbon-inside:before {
        width: 0;
        height: 0;
        border-left: 7px solid transparent;
        border-right: 7px solid transparent;
        border-bottom: 10px solid #090909;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        content: "";
        top: 35px;
        transform: rotate(-45deg);
    }

    .cover-ribbon .cover-ribbon-inside:after {
        width: 0;
        height: 0;
        border-top: 7px solid transparent;
        border-left: 10px solid #090909;
        border-bottom: 7px solid transparent;
        position: absolute;
        left: 95%;
        right: 0;
        top: 34px;
        bottom: 0;
        content: "";
        transform: rotate(-45deg);
    }

    div#errors {
        text-align: center;
        color: red;
        font-size: 20px;
    }

    /* div#qr-code-listing {
        display: flex;
        justify-content: center;
    } */


    #qr-code-listing .card-1 {
        border-radius: 10px;
        box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.3);
        width: 95%;
        height: 130px;
        background-color: #ffffff;
        padding: 0px 10px 10px;
        }
        #qr-code-listing  .card-1 h3 {
    font-size: 22px;
    font-weight: 600;

    }
    #qr-code-listing .drop_box {
        margin: 10px 0;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border: 3px dotted #a3a3a3;
        border-radius: 5px;
        }

#qr-code-listing
.drop_box h4 {
font-size: 16px;
font-weight: 400;
color: #2e2e2e;
}

.drop_box p {
margin-top: 10px;
margin-bottom: 20px;
font-size: 12px;
color: #a3a3a3;
}

.btn {
text-decoration: none;
background-color: #005af0;
color: #ffffff;
padding: 10px 20px;
border: none;
outline: none;
transition: 0.3s;
}

.btn:hover{
text-decoration: none;
background-color: #ffffff;
color: #005af0;
padding: 10px 20px;
border: none;
outline: 1px solid #010101;
}
.form input {
margin: 10px 0;
width: 100%;
background-color: #e2e2e2;
border: none;
outline: none;
padding: 12px 20px;
border-radius: 4px;
}
#qr-canvas-visible {

width: 322px !important;
height: 150px !important;
display: inline-block;

}

div#reader {
display: flex;
justify-content: center;
}

img#img {
    height: 50px;
    width: 100px;
}
    /* css loader ends */
</style>
