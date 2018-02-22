<style type="text/css">
    label {
        padding: 10px;
        margin: 4px;
        font-size: 16px;
    }

    input[type="checkbox"] {
        margin: 0 5px;
    }

    #prop {
        padding: 15px;
        margin: 15px;
    }

    .bottom {
        padding: 10px 16px;
        margin: 4px;
    }
    
</style>

<div id="prop">
    <h1>Fusion Connector</h1>
    <br>
    <form class="form-inline" id="saveSettingsForm" action="" method="POST">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="vtiger_login">{vtranslate('LBL_ADMIN_LOGIN', 'Calls')}: </label>
                    <input class="form-control" type="text" name="vtiger_login" id="vtiger_login" />
                </div>
                <div class="form-group">
                    <label for="vtiger_ac">{vtranslate('LBL_ADMIN_ACCESS_KEY', 'Calls')}: </label>
                    <input class="form-control" type="text" name="vtiger_ac" id="vtiger_ac" />
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="fusionUrl">FusionPBX URL: </label>
                    <input class="form-control" type="text" name="fusionUrl" id="fusionUrl" />
                </div>
                <div class="form-group">
                    <label for="fusionApi">FusionPBX API key: </label>
                    <input class="form-control" type="text" name="fusionApi" id="fusionApi" />
                </div>
            </div>
            <div class="col-sm-4"></div>

        </div>
        <br>
        <label>{vtranslate('LBL_FIELDS_TO_DISPLAY', 'Calls')}: </label>

        <div class="row">      
            <div class="col-sm-4">
                
                <div class="checkbox">
                    <label><input type="checkbox" value="" name="firstname">{vtranslate('firstname', 'Calls')}</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="" name="lastname">{vtranslate('lastname', 'Calls')}</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="" name="email">{vtranslate('email', 'Calls')}</label>
                </div>          
                <br>
                <div class="checkbox">
                    <label><input type="checkbox" value="" name="company">{vtranslate('company', 'Calls')}</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="" name="department">{vtranslate('department', 'Calls')}</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" value="" name="phone">{vtranslate('phone', 'Calls')}</label>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-4">
                <button type="button" id="button_save" class="btn btn-success bottom" onclick="saveProperties()">{vtranslate('LBL_SAVE', 'Calls')}</button>
                <button type="button" class="btn btn-primary bottom" onclick="back()">{vtranslate('LBL_BACK', 'Calls')}</button>
            </div>
        </div>
   
    </form>
    <br>
    

    <script type="text/javascript">
        var json = '{php} echo file_get_contents( getcwd() . "/properties.json"); {/php}';
        var properties = JSON.parse(json);

        if (typeof properties.shownFields != 'undefined') {
            $("#saveSettingsForm input:checkbox").each(function () {
                if (properties.shownFields.indexOf( $(this).attr("name") ) >= 0) {
                    $(this).attr("checked", "checked");
                }
            });
        }

        $("#vtiger_login").val( properties.vtiger_login );
        $("#vtiger_ac").val( properties.vtiger_ac );
        $("#fusionApi").val( properties.fusion_api_key );
        $("#fusionUrl").val( properties.fusion_url );

        $("#saveSettingsForm").attr("action", properties.connector_url + "saveSettings.php");

        function back() {
            window.location = "index.php?module=Calls&view=List&app=PROJECT";
        }

        function saveProperties() {
            console.log("saved");
            $("#button_save").text("{vtranslate('LBL_SAVE_SUCCESS', 'Calls')}");
            setTimeout(function () {
                $("#saveSettingsForm").submit();
            }, 1500);
        }


    </script>
</div>