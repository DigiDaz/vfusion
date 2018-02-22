{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

<footer class="app-footer">

    <style type="text/css">

    #phone_panels_container {
        position: fixed;
        bottom: 64px;
        right: 64px;

        z-index: 9999;
    }

    .phone_panel {
        width: 320px;
        height: auto;
        padding: 5px;
        margin: 3px;

        background-color: #C8CBCC;
        border-radius: 8px;

        display: none;
    }

    .phone_panel h2 {
        text-align: center;
    }

    .phone_panel_details {
        padding-left:15px;
        font-size: 18px;
        list-style: none;
        display: none;
    }

    .phone_panel_create {
        font-size: 18px;
        padding: 10px;
        display: none;
    }

    .phone_panel_create .form-group {
        padding: 5px 0;
    }

    #phone_panel_call {
        width: auto;
        height: auto;
        max-width: 120px;
        
        padding: 10px 20px;

        border-radius: 4px;
        background-color: #C8CBCC;

        display: none;
        position: absolute;

        font-size: 14px;

        z-index: 99999;
    }

    #phone_panel_call button {
        padding: 10px 30px;
        border-radius: 4px;
    }

    .btn-group {
        padding-top: 8px;
    }

    .btn-group button {
        border-radius: 4px;
    }

</style>

<div id="phone_panels_container">

</div>

<div id="phone_panel_call">
    <button type="button" class="btn btn-success" onclick="clickToCall()">Call</button>
    <form id="call_form">
        <input type="hidden" id="number_call" name="number" value="">
        <input type="hidden" name="user_id" value="{$smarty.session.authenticated_user_id}">
    </form>
</div>

<div id="phone_panel_template" style="display: none;" datasort="">
    <h2>{vtranslate('IncomingСall', 'Calls')}</h2>
    <!-- Existsing contact details -->
    <ul id="_details" class="phone_panel_details">
        <li id="_firstname"></li>
        <li id="_lastname"></li>
        <li id="_mobile"></li>
        <li id="_email"></li>
        <li id="_title"></li>
        <li id="_department"></li>
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <button class="btn btn-primary proceed_to_contact">{vtranslate('LBL_PROCEED_TO_CONTACT', 'Calls')}</button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-danger hide_phone_panel" onclick="cancel()">{vtranslate('LBL_CANCEL', 'Calls')}</button>        
            </div>
        </div>
    </ul>
    

    <!-- Panel to create new contact instance -->
    <div id="_unknown" class="phone_panel_create">
        <h4><i>{vtranslate('LBL_UNKNOWN_CONTACT', 'Calls')}</i></h4>
        <span id="_phone"></span>
        <br>
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <button type="button" class="btn btn-primary create_contact" onclick="createContact()">{vtranslate('LBL_CREATE_CONTACT', 'Calls')}</button>
                <input type="hidden" name="mobile" id="_mobile">
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-danger hide_phone_panel" onclick="cancel()">{vtranslate('LBL_CANCEL', 'Calls')}</button>        
            </div>
        </div>
    </div>

</div>


        {* SalesPlatform.ru begin *}
        <div class="pull-right footer-icons">
            <small>{vtranslate('LBL_CONNECT_WITH_US', 'Calls')}&nbsp;</small>
            <!-- <a href="http://community.salesplatform.ru/"><img src="layouts/vlayout/skins/images/forum.png"></a> -->
            &nbsp;<a href="https://twitter.com/salesplatformru"><img src="layouts/vlayout/skins/images/twitter.png"></a>
        </div>
        {* SalesPlatform.ru end *}
	<p>
		{* SalesPlatform begin*}
                {*Powered by vtiger CRM - 7.0&nbsp;&nbsp;© 2004 - {date('Y')}&nbsp;&nbsp;*}
                {*<a href="//www.vtiger.com" target="_blank">Vtiger</a>&nbsp;|&nbsp;*}
                {*<a href="https://www.vtiger.com/privacy-policy" target="_blank">Privacy Policy</a>*}
                
            {vtranslate('POWEREDBY')} {$VTIGER_VERSION} &nbsp;
            &copy; 2004 - {date('Y')}&nbsp&nbsp;
            <a href="//www.vtiger.com" target="_blank">vtiger.com</a>
            &nbsp;|&nbsp;
            {* SalesPlatform.ru begin Doc links fixed *}
            &copy; 2011 - {date('Y')}&nbsp&nbsp;
            <a href="//salesplatform.ru/" target="_blank">SalesPlatform.ru</a>
            {*SalsePlatform end *}
	</p>
</footer>
</div>
<div id='overlayPage'>
	<!-- arrow is added to point arrow to the clicked element (Ex:- TaskManagement), 
	any one can use this by adding "show" class to it -->
	<div class='arrow'></div>
	<div class='data'>
	</div>
</div>
<div id='helpPageOverlay'></div>
<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>
<div class="modal myModal fade"></div>
        
        <script type="text/javascript">

            var flag = true;

            var modulename = getUrlParameter("module");
            if (typeof modulename !== 'undefined') {
                if (modulename.localeCompare("Calls") === 0) {
                    $("#appnav").hide();
                }
            }
            

            var connector_url;

            //Check if we are not on the login page
            if ( !$("#login_page_indicator").length ) {

                var json = '{php} echo file_get_contents( getcwd() . "/properties.json"); {/php}';
                var properties = JSON.parse(json);

                var userPhone;
                $.get(
                    "index.php",
                    {
                        module: "Calls",
                        action: "GetUserPhone",
                        userId: $("input[name='user_id']").val()
                    },
                    function (data) {
                        userPhone = data.result;
                    }
                );

                // if (typeof properties.vtiger_ac == 'undefined') window.location.href = properties.vtiger_url + "index.php?modules=Calls&view=Settings&app=PROJECT";

                //Set up events source which returns record of unaswered call or nothing
                var eventSource = new EventSource(properties.connector_url + "eventHandler.php");
                //Handle response from event source
                function process() {
                    $.get(
                        properties.connector_url + "eventHandler.php",
                        function (json) {
                            data = JSON.parse(json);
                            // console.log(data);
                            for (var i = 0; i < data.length; i++) {
                                call = data[i];
                                // console.log("Length = " + $(".phone_panel").length);
                                if ( $(".phone_panel").length < 4) {
                                    // console.log("process");
                                    processCall(call);
                                }
                            }
                        }
                    );

                    $(".fieldValue span[data-field-type='phone']").attr("flagged", false);

                    {literal}
                    if ($(".fieldValue span[data-field-type='phone']").length > 0) {
                        // console.log($(this).attr("flagged"));
                        if ($(this).is("flagged") === false) {
                            $(".fieldValue span[data-field-type='phone']").hover(function () {
                                $("#number_call").val($.trim($(this).text()));
                                var number = $.trim($(this).text());
                                // console.log("click to call " + $("#number_call").val());

                                var element = $(this).offset();
                                if (number !== '') {
                                    // console.log("click to call " + number);                                
                                    $("#phone_panel_call").css("left", element.left + 100).css("top", element.top - 12);
                                    if (/^\+?[0-9-\(\)\ \#pw]{1,50}$/.test(number)) {
                                        $("#phone_panel_call").show();
                                    }
                                }  
                            });
                        }
                        $(this).attr("flagged", true);
                    }
                    {/literal}                    

                    sortPanels();

                    setTimeout(process, 200);
                }
                setTimeout(process, 200);


                function processCall(call) {
                    // console.log(call.number_to);
                    // console.log(userPhone);
                    if (call.uuid != -1 && call.number_to === userPhone && call.direction.localeCompare("local") !== 0) {
                        // console.log(call.uuid);
                        //Check if this phone panel is already present
                        if ( !$("#" + call.uuid).length ) {
                            //try to find related contact by number
                            $.get(properties.connector_url + "getContact.php?number_from=" + call.number_from, function(data) {
                                
                                var info = JSON.parse(data);
                                // console.log(info);
                                var panel = $("#phone_panel_template").clone();
                                panel.attr("id", call.uuid);
                                panel.addClass("phone_panel");
                                panel.show();

                                //Insert call start time to sort elements
                                panel.attr("datasort", call.start_time);

                                //We have found contact with caller number and we want to show it's info
                                if (info.error == "false") {
                                    panel.find("#_details").attr("id", call.uuid + "_details").show();

                                    panel.find("#_firstname").attr("id", call.uuid + "_firstname");
                                    panel.find("#_lastname").attr("id", call.uuid + "_lastname");
                                    panel.find("#_mobile").attr("id", call.uuid + "_mobile");
                                    panel.find("#_email").attr("id", call.uuid + "_email");
                                    panel.find("#_title").attr("id", call.uuid + "_title");
                                    panel.find("#_department").attr("id", call.uuid + "_department");

                                    if (properties.shownFields.indexOf("firstname") >= 0)   panel.find("#" + call.uuid + "_firstname").text("{vtranslate('firstname', 'Calls')}: " + info.firstname);
                                    if (properties.shownFields.indexOf("lastname") >= 0)    panel.find("#" + call.uuid + "_lastname").text("{vtranslate('lastname', 'Calls')}: " + info.lastname);
                                    if (properties.shownFields.indexOf("phone") >= 0)       panel.find("#" + call.uuid + "_mobile").text("{vtranslate('Phone', 'Calls')}: " + info.mobile);
                                    if (properties.shownFields.indexOf("email") >= 0)       panel.find("#" + call.uuid + "_email").text("{vtranslate('Email', 'Calls')}: " + info.email);
                                    if (properties.shownFields.indexOf("company") >= 0)     panel.find("#" + call.uuid + "_company").text("{vtranslate('Company', 'Calls')}: " + info.mobile);
                                    if (properties.shownFields.indexOf("department") >= 0)  panel.find("#" + call.uuid + "_department").text("{vtranslate('Department', 'Calls')}: " + info.mobile);
                                    
                                    panel.find(".proceed_to_contact").attr("onclick", "proceedToContact(" + info.contactid + ", '" + call.uuid + "')");
                                    panel.find(".hide_phone_panel").attr("onclick", "cancel('"+ call.uuid +"')");
                                }
                                //No contact has been found - show form to create one
                                else {
                                    panel.find("#_unknown").attr("id", call.uuid + "_unknown").show();

                                    panel.find("#_phone").attr("id", call.uuid + "_phone").text("{vtranslate('Phone', 'Calls')}: " + call.number_from);
                                    panel.find("#_mobile").attr("id", call.uuid + "_mobile").val(call.number_from);
                                    panel.find(".create_contact").attr("onclick", "createContact('"+ call.uuid +"', '"+ call.number_from +"')");
                                    panel.find(".hide_phone_panel").attr("onclick", "cancel('"+ call.uuid +"')");
                                }
                                
                                
                                panel.appendTo("#phone_panels_container");

                                
                            });
                        }
                        else {
                        }
                    }
                }

                {literal}
                $("[data-field-type=phone]").mouseenter(function(){

                    if (!$(this).hasClass("listSearchContributor")) {
                        var element = $(this).offset();
                        var number = $(this).attr("data-rawvalue");
                        if (number !== '') {
                            $("#phone_panel_call").css("left", element.left + 100).css("top", element.top - 12);
                            if (/^\+?[0-9-\(\)\ \#pw]{1,50}$/.test(number)) {
                                $("#phone_panel_call").show();
                            }
                        }           
                        
                    }      

                });
                {/literal}

                $("[data-field-type=phone]").hover(function() {
                    if (!$(this).hasClass("listSearchContributor")) {
                        $("#number_call").val($(this).attr("data-rawvalue"));  
                        if ($(this).attr("data-rawvalue") === undefined) {
                            $("#number_call").val($(this).text());
                        }
                    }
                });


                $("#phone_panel_call").mouseleave(function() {
                    $("#phone_panel_call").hide();
                });


            }

            function createContact(uuid, phone) {
                $.post(properties.connector_url + "updateAnswered.php", 
                    {
                        uuid: uuid
                    },
                    function(json) {
                        window.location.href = 'index.php?module=Contacts&view=Edit&app=MARKETING&phone=' + encodeURIComponent(phone);
                    }
                );
            }

            function proceedToContact(id, uuid) {
                $.post(properties.connector_url + "updateAnswered.php", 
                    {
                        uuid: uuid,
                    },
                    function(json) {
                        // console.log(json);
                        window.location.href = 'index.php?module=Contacts&view=Edit&app=MARKETING&record=' + id;
                    }
                );
            }

            function cancel(uuid) {
                $.post(properties.connector_url + "updateAnswered.php", 
                    {
                        uuid: uuid
                    }, 
                    function (data) {
                    }
                );
                $("#" + uuid).remove();
            }

            function clickToCall() {
                $.post(properties.connector_url + "click_to_call.php", $("#call_form").serialize(), function(data) {
                    // console.log(data);
                    var params = JSON.parse(data);
                    var url = params.url + "app/click_to_call/click_to_call.php"
                    // console.log(url);
                    $.get(url, 
                        { 
                            src : params.src,
                            dest : params.dest,
                            key : params.key
                        },
                        function (data) {
                            console.log(data);
                        }
                    );
                });
            }

            function getUrlParameter(sParam) {
                var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');

                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : sParameterName[1];
                    }
                }
            }

            function sortPanels() {
                //sort all panels to show newest ones
                function sorter(a, b) {
                    var start_time_a = new Date($(a).attr("datasort"));
                    var start_time_b = new Date($(b).attr("datasort"));

                    var result = (start_time_a.getTime() < start_time_b.getTime()) ? -1 : (start_time_a.getTime() > start_time_b.getTime()) ? 1 : 0;
                    return result;
                }

                var panels = $(".phone_panel").toArray().sort(sorter);

                $.each(panels, function (i, v) {
                    if (i < 4)
                        $("#phone_panels_container").append(v);
                    else
                        $(v).css("display", "none");
                });
            }
        
        </script>

{include file='JSResources.tpl'|@vtemplate_path}
</body>

</html>