[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3a12c379-2634-4c86-bd00-60bb8f130763/big.png)](https://insight.sensiolabs.com/projects/3a12c379-2634-4c86-bd00-60bb8f130763)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/9e7ca719cbb240ec8dbb5500b39c73a2)](https://www.codacy.com/app/LEXASOFT/PHPCI-Telegram-Plugin)
# PHPCI-Telegram-Plugin
Telegram plugin for PHPCI
# Installation
First of all - `composer require lexasoft/phpci-telegram-plugin`

# Add to project
In the PHPCI Project config section add the Telegram trigger
```
complete:
    telegram:
        api_key: "<YOUR_BOT_TOKEN_HERE>"
        message: [%ICON_BUILD%] [%PROJECT_TITLE%](%PROJECT_URI%) - [Build #%BUILD%](%BUILD_URI%) has finished for commit [%SHORT_COMMIT% (%COMMIT_EMAIL%)](%COMMIT_URI%) on branch [%BRANCH%](%BRANCH_URI%)
        recipients:
            - <user id>
            - "-<group id>"
            - "@<channel id>"
        send_log: true
```
