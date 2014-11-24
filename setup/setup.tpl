<!DOCTYPE html>
<html>
    <head>
        <title><tag:software /> Setup</title>

        <link rel="icon" type="image/png" href="../resources/img/icon.png" />

        <link rel="stylesheet" type="text/css" href="../external/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../resources/css/layout.css" />
        <link rel="stylesheet" type="text/css" href="../resources/css/winter.css" />

        <script src="../external/jquery-1.7.js"></script>
        <script src="setup.js"></script>

        <style type="text/css">
        .input .help-block {
            clear: both;
        }
        </style>

        <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
    </head>

    <body id="module">

        <div id="headerContainer">
            <div id="header" style='width: 100%;'>
                <img src="../resources/img/logo.png" class="logo" border="0" alt="UberTweak Logo" />
                <div class="title">
                &Uuml;berSite Setup<br />
                <div class="version">Version <tag:version /> <if:codename>(<tag:codename />)</if:codename></strong></div></div>
            </div>
        </div>

        <div id="content">

            <h2>Prerequisite Check:</h2>

            Welcome to <tag:software />. This short setup process will help configure your website - simply enter in the information for each tab.
            Once all tabs are complete, open the final tab in order to save your configuration and proceed to the next configuration stage.
            <br />
            <ul>
                <li>PHP version at least 5.5... <tag:checks.php /></li>
                <li>Config directory writable... <tag:checks.configWritable /></li>
                <li>MySQL available... <tag:checks.mysql /></li>
            </ul>

            <if:error>
            <h3>An essential requirement is not available: the setup process cannot continue.</h3>
            <else:error>

            <h2>Configuration:</h2>

            <form>
                <div class="row">
                    <div class="span4">
                    Basic information about the camp: this information will be used for display purposes.
                    </div>

                    <div class="span12">
                        <div class="clearfix">
                            <label for="campName">Camp name:</label>
                            <div class="input">
                                <input id="campName" class="span4" type="text" name="campName" placeholder="&Uuml;bertweak Summer" required/>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="campYear">Camp year:</label>
                            <div class="input">
                                <input id="campYear" class="span1" type="text" name="campYear" maxlength="4" placeholder="2011" pattern="[0-9]{4}" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="span4">
                    <tag:software /> uses a MySQL database to store data. Once the information has been verified as correct, the database structure will be created.<br /><br />
                    If the selected database does not already exist, make sure the user has CREATE DATABASE permissions.
                    </div>

                    <div class="span12">
                        <div class="clearfix">
                            <label for="mysqlHost">MySQL Host:</label>
                            <div class="input">
                                <input id="mysqlHost" class="span4" type="text" name="mysqlHost" placeholder="localhost" required />
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="mysqlUser">Username:</label>
                            <div class="input">
                                <input id="mysqlUser" class="span4" type="text" name="mysqlUser" placeholder="root" required />
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="mysqlPassword">Password:</label>
                            <div class="input">
                                <input id="mysqlPassword" class="span4" type="password" name="mysqlPassword" placeholder="password" required />
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="mysqlDatabase">Database:</label>
                            <div class="input">
                                <input id="mysqlDatabase" class="span4" type="text" name="mysqlDatabase" placeholder="ubertweak_sp11" required />
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="input">
                                <input type="submit" class="btn" value="Verify and save configuration" id="save-config" />
                            </div>
                        </div>

                        <div class="alert-message info" id="processing" style="display: none;">Processing...</div>
                        <br />
                        <input type="submit" class="btn primary" value="Go to the website!" id="refresh-page" style="display: none;" />
                    </div>
                </div>

             </form>

            </if:error>

        </div>

    </body>
</html>
