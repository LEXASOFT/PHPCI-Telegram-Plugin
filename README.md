# PHPCI-Telegram-Plugin
Telegram plugin for PHPCI
# Installation
Place the file under the folder <path to PHPCI>/PHPCI/plugins

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
        sendlog: true
```