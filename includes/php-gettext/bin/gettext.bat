@echo off
xgettext -kT_ngettext:1,2 -kT_ -L PHP -o ..\..\locales\messages.po ..\..\..\about.php ..\..\..\access.php ..\..\..\accessaccount.php ..\..\..\activate.php ..\..\..\add.php ..\..\..\alltags.php ..\..\..\bheader.php ..\..\..\books.php ..\..\..\books_js.php ..\..\..\books_nojs.php ..\..\..\changepass.php ..\..\..\checkGroup.php ..\..\..\checkUsername.php ..\..\..\comment.php ..\..\..\conn.php ..\..\..\controlpanel.php ..\..\..\deleteaccount.php ..\..\..\deleteallbooks.php ..\..\..\emailpass.php ..\..\..\export.php ..\..\..\feed.php ..\..\..\footer.php ..\..\..\publicfooter.php ..\..\..\forgotpass.php ..\..\..\gcreate.php ..\..\..\gdelete.php ..\..\..\gdetails.php ..\..\..\gedit.php ..\..\..\gheader.php ..\..\..\gjoin.php ..\..\..\gmanage.php ..\..\..\gmembers.php ..\..\..\groups.php ..\..\..\gunsubs.php ..\..\..\header.php  ..\..\..\import.php ..\..\..\importDelicious.php ..\..\..\index.php ..\..\..\login.php ..\..\..\logout.php ..\..\..\managenews.php ..\..\..\manageusers.php ..\..\..\modifyaccount.php ..\..\..\modifyfav.php ..\..\..\modifyfolder.php ..\..\..\netscape_import.php ..\..\..\news.php ..\..\..\newsdetails.php ..\..\..\newsmodify.php ..\..\..\newuser.php ..\..\..\onlineusers.php ..\..\..\populartags.php ..\..\..\psearch.php ..\..\..\recent_tags.php ..\..\..\redirect.php ..\..\..\removeactivations.php ..\..\..\removeactivations_delete.php ..\..\..\sbheader.php ..\..\..\sbmostused.php ..\..\..\sbsearches.php ..\..\..\search.php ..\..\..\spamcenter.php ..\..\..\statsb.php ..\..\..\statsexportimport.php ..\..\..\tags_rightmenu.php ..\..\..\tags.php ..\..\..\umodifyaccount.php ..\..\..\userb.php ..\..\bookmarks.php ..\..\dynamicTags.php ..\..\easybook_content.php ..\..\f_deleteaccount.php ..\..\folders.php ..\..\gdetails_body.php ..\..\pagenb.php ..\..\searchform.php ..\..\tags_functions.php ..\..\user.php ..\..\..\templates\publicb.tpl.php ..\..\jquery\bookmarksjs.php ..\..\..\error.php ..\..\..\manageconfig.php ..\..\convert_date.php ..\..\ff_extension.php
if /i "%1" == "-p" goto stats
if exist "..\..\locales\%1.po" goto merge
echo "Usage: $0 [-p|<basename>]"
goto end

:stats
msgfmt --statistics ..\..\locales\messages.po
goto end

:merge
msgmerge -o ..\..\locales\tmp%1.po ..\..\locales\%2\LC_MESSAGES\%1.po ..\..\locales\messages.po
if exist "..\..\locales\%1.po" rename ..\..\locales\%1.po %1.po.bak
rename ..\..\locales\tmp%1.po %1.po
if exist "..\..\locales\%1.po.bak" del ..\..\locales\%1.po.bak
msgfmt --statistics "..\..\locales\%1.po"

:end
echo Finished