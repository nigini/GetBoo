@echo off
call gettext messages en_US
move ..\..\locales\messages.po ..\..\locales\en_US\LC_MESSAGES\messages.po
call gettext messages fr_FR
move ..\..\locales\messages.po ..\..\locales\fr_FR\LC_MESSAGES\messages.po
call gettext messages cs_CZ
move ..\..\locales\messages.po ..\..\locales\cs_CZ\LC_MESSAGES\messages.po
call gettext messages es_ES
move ..\..\locales\messages.po ..\..\locales\es_ES\LC_MESSAGES\messages.po
call gettext messages de_DE
move ..\..\locales\messages.po ..\..\locales\de_DE\LC_MESSAGES\messages.po
del messages.mo
echo Done
pause