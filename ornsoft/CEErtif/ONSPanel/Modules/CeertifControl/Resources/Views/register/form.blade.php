@extends('coucoulab::layouts.front-novue')

@push("head")

    <style>

        .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
            background-color: #c8c8c8 !important;
        }

        .tbl-role td {
            border-top: none;
        }

        .btn-register-container {
            width:100%;
            margin-top: 15px;
        }

        .btn-register {
            width:90%;
        }

        .radio-toolbar input[type="radio"] {
            opacity: 0;
            position: fixed;
            width: 0;
        }

        .radio-toolbar label {
            color: white;
            display: inline-block;
            background-color: #A6A6A6;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 2px;
            width:130px;
            text-align: center;
        }

        /*Targeting the laboratory button*/
        .radio-toolbar label:nth-last-child(1){
            float:right;
        }

        .radio-toolbar input[type="radio"]:checked + label {
            background-color:#FF9300;
        }

        .radio-toolbar-container{
            position: relative;
            top: 15px;
            border:0px solid black;
        }

        .form_container {
            margin-top: 0;
        }
        .form-inputs {
            margin-top: 25px;
        }
        .pick-profile {
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center
        }
        .invalid-feedback {
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #a94442;
        }


    </style>
@endpush
@section("content")

    <div class="body-page" >
        <div class="container">
            @include('core::partials.flash-messages')

            <div class="user_card">
                <div class="d-flex justify-content-center form_container">
                    {!! Form::open(['route' => ['r.register'] , 'role'=>"form", 'method'=>"POST"] ) !!}

                    <div class="form-group radio-toolbar-container">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 pick-profile">
                                {{ trans('coucoulab::coucoulab.form.register.pick-profile') }}
                            </div>
                        </div>
                        <div class="radio-toolbar">
                            <input type="radio" id="radioDentist" name="role" value="dentist">
                            <label for="radioDentist">{{ trans('coucoulab::coucoulab.form.register.dentist') }}</label>

                            <input type="radio" id="radioLaboratory" name="role" value="laboratory">
                            <label for="radioLaboratory">{{ trans('coucoulab::coucoulab.form.register.laboratory') }}</label>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                @if ($errors->has('role'))
                                    <div style="margin-top: 10px;">
                                        {{ $errors->first('role') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="form-inputs select-type-dentist" hidden>
                        <div class="form-group mb-3">
                            <label class="radio-inline"><input type="radio" id="selectDentist" name="optradio" checked>{{ trans('coucoulab::coucoulab.form.register.dentist-register') }}</label>
{{--                            <br/>--}}
{{--                            <label class="radio-inline"><input type="radio" id="selectEmployee"  name="optradio">{{ trans('coucoulab::coucoulab.form.register.employee') }}</label>--}}
                        </div>
                    </div>

                    <div class="form-inputs alert alert-warning" role="alert" hidden>
                        {{ trans('coucoulab::coucoulab.form.register.message-employee') }}
                    </div>

                    <div class="form-inputs form_register">
                        <div class="form-group mb-3">
                            <label id="label_name" for="name" class="control-label"></label>
                            <input id="name" type="text" class="form-control form-control-lg {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name') }}" autofocus>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-3 for-dentist">
                            <label for="lastname" class="control-label">{{ trans('user::auth.form.lastname') }}</label>
                            <input id="lastname" type="text" class="form-control form-control-lg {{ $errors->has('lastname') ? 'is-invalid' : '' }}" name="lastname" value="{{ old('lastname') }}" autofocus>
                            @if ($errors->has('lastname'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('lastname') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">{{ trans('user::auth.form.email') }}</label>
                            <input id="email" type="email" class="form-control form-control-lg {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label"> {{ trans('user::auth.form.password') }} </label>
                            <input id="password" type="password" class="form-control form-control-lg {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password">
                            @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                            <p class="help-block" style="font-size: 12px">{{ trans('user::auth.form.password-help') }}</p>

                        </div>
                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="control-label"> {{ trans('user::auth.form.password_confirmation') }} </label>
                            <input id="password-confirm" type="password" class="form-control form-control-lg {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" name="password_confirmation">

                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password_confirmation') }}
                                </div>
                            @endif

                        </div>

                        <div class="form-group mb-3 for-laboratory">
                            <label for="street" class="control-label">{{ trans('coucoulab::laboratories.form.street') }}</label>
                            <input id="street" type="text" class="form-control form-control-lg {{ $errors->has('street') ? 'is-invalid' : '' }}" name="street" value="{{ old('street') }}" autofocus>
                            @if ($errors->has('street'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('street') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-3 for-laboratory">
                            <label for="city" class="control-label">{{ trans('coucoulab::laboratories.form.city') }}</label>
                            <input id="city" type="text" class="form-control form-control-lg {{ $errors->has('city') ? 'is-invalid' : '' }}" name="city" value="{{ old('city') }}" autofocus>
                            @if ($errors->has('city'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('city') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-3 for-laboratory">
                            <label for="zip" class="control-label">{{ trans('coucoulab::laboratories.form.zip_code') }}</label>
                            <input id="zip" type="number" class="form-control form-control-lg {{ $errors->has('zip') ? 'is-invalid' : '' }}" name="zip" value="{{ old('zip') }}" autofocus>
                            @if ($errors->has('zip'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('zip') }}
                                </div>
                            @endif
                        </div>

                        <!--
                        <div class="form-group mb-3 for-laboratory">
                            <label for="state" class="control-label">{{ trans('coucoulab::laboratories.form.state') }}</label>
                            <input id="state" type="text" class="form-control form-control-lg {{ $errors->has('state') ? 'is-invalid' : '' }}" name="state" value="{{ old('state') }}" autofocus>
                            @if ($errors->has('state'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('state') }}
                                </div>
                            @endif
                        </div>
                        -->

                        <div class="form-group mb-3 for-laboratory">
                            <label for="country" class="control-label">{{ trans('coucoulab::laboratories.form.country') }}</label>
                            <select id="country" name="country"  class="form-control">
                                <option value="Afganistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antigua & Barbuda">Antigua & Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bonaire">Bonaire</option>
                                <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Canary Islands">Canary Islands</option>
                                <option value="Cape Verde">Cape Verde</option>
                                <option value="Cayman Islands">Cayman Islands</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Channel Islands">Channel Islands</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos Island">Cocos Island</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo">Congo</option>
                                <option value="Cook Islands">Cook Islands</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote DIvoire">Cote DIvoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaco">Curacao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                                <option value="East Timor">East Timor</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Falkland Islands">Falkland Islands</option>
                                <option value="Faroe Islands">Faroe Islands</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France" selected>France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Ter">French Southern Ter</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Great Britain">Great Britain</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Hawaii">Hawaii</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea North">Korea North</option>
                                <option value="Korea Sout">Korea South</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macau">Macau</option>
                                <option value="Macedonia">Macedonia</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Midway Islands">Midway Islands</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Nambia">Nambia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherland Antilles">Netherland Antilles</option>
                                <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                <option value="Nevis">Nevis</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau Island">Palau Island</option>
                                <option value="Palestine">Palestine</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Phillipines">Philippines</option>
                                <option value="Pitcairn Island">Pitcairn Island</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of Montenegro">Republic of Montenegro</option>
                                <option value="Republic of Serbia">Republic of Serbia</option>
                                <option value="Reunion">Reunion</option>
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="St Barthelemy">St Barthelemy</option>
                                <option value="St Eustatius">St Eustatius</option>
                                <option value="St Helena">St Helena</option>
                                <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                <option value="St Lucia">St Lucia</option>
                                <option value="St Maarten">St Maarten</option>
                                <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
                                <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
                                <option value="Saipan">Saipan</option>
                                <option value="Samoa">Samoa</option>
                                <option value="Samoa American">Samoa American</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome & Principe">Sao Tome & Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Swaziland">Swaziland</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                                <option value="Tahiti">Tahiti</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks & Caicos Is">Turks & Caicos Is</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Erimates">United Arab Emirates</option>
                                <option value="United States of America">United States of America</option>
                                <option value="Uraguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Vatican City State">Vatican City State</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                <option value="Wake Island">Wake Island</option>
                                <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zaire">Zaire</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                            @if ($errors->has('country'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('country') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-3 for-laboratory">
                            <label for="phone" class="control-label">{{ trans('coucoulab::laboratories.form.phone') }}</label>
                            <input id="phone" type="text" class="form-control form-control-lg {{ $errors->has('name') ? 'is-invalid' : '' }}" name="phone"  autofocus>
                            @if ($errors->has('phone'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>


                        <div class="row">
                            <div class="col-sm-8">
                                <div class="checkbox-custom checkbox-default">
                                    <input id="agree_terms" name="agree_terms" type="checkbox" value="1" required>
                                    <label for="agree_terms">{{ trans('user::auth.form.agree') }} <a href="/term-conditions">{{ trans('user::auth.form.terms') }}</a></label>
                                </div>
                            </div>
                            <div class="text-center btn-register-container">
                                <button type="submit" class="btn btn-primary btn-lg btn-register">
                                    {{ trans('user::auth.btn.register') }}
                                </button>
                            </div>
                        </div>
                        <br/>
                        <p class="text-center">{{ trans('user::auth.message.yes-account') }} <a href="/login">{{ trans('user::auth.btn.sign-in') }}</a></p>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@if(!empty($_GET["t"]))
    <script>
        window.referrer = "<?php echo $_GET['t']; ?>";
        console.log('this is m referrer', window.referrer)
    </script>
@else
    <script>
        window.referrer = null;
    </script>
@endif

@push("scripts")
<script>
    var translations = {
        name: "@lang('user::auth.form.name')",
        lab_name: "@lang('user::auth.form.lab_name')",
    };

    $('#radioDentist').click(function () {
        localStorage.setItem('radio', '#radioDentist');
        $('.select-type-dentist').show();
        $('.for-dentist').show();
        $('.for-laboratory').hide();
        $("#label_name").html(translations.name);
        $("#lastname").val("");
    });

    $('#radioLaboratory').click(function () {
        localStorage.setItem('radio', '#radioLaboratory');
        $('.select-type-dentist').hide();
        $('.alert-warning').hide();
        $('.for-dentist').hide();
        $('.for-laboratory').show();
        $("#label_name").html(translations.lab_name);
        $("#lastname").val("_");
    });

    $('#selectDentist').click(function () {
        $('.alert-warning').hide();
        $('.form_register').show();
    });

    $('#selectEmployee').click(function () {
        $('.form_register').hide();
        $('.alert-warning').show();
    });


    $(document).ready(function(){
        @if ( $errors->any())
            let radioUserType = localStorage.getItem('radio');
            if(radioUserType != null && radioUserType == '#radioDentist') $('.for-laboratory').hide();
            let radioButton = localStorage.getItem('radio');
            $(radioButton).prop('checked', true);

            if($('#radioDentist').is(':checked')) {
                $("#label_name").html(translations.name);
            } else {
                $("#label_name").html('Lab Name');
                $('.for-dentist').hide();
            }

            $('.form-inputs').show();
            $('.select-type-dentist').hide();
            $('.alert-warning').hide();
        @else
            $('.form-inputs').hide();
        @endif

        if(window.referrer == 'lab'){
            $('#radioLaboratory').trigger('click');
        }
        if(window.referrer == 'den'){
            $('#radioDentist').trigger('click');
        }
    });

    $('.radio-toolbar').click(function() {
        $('.form_register').show();
    });

</script>

@endpush
