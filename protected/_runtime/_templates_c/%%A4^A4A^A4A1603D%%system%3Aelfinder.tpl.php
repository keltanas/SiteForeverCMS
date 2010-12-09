<?php /* Smarty version 2.6.26, created on 2010-10-07 10:11:03
         compiled from system:elfinder.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>elFinder</title>
	<!--
	<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
	-->
	<link rel="stylesheet" href="<?php echo $this->_tpl_vars['path']['misc']; ?>
/smoothness/jquery-ui.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?php echo $this->_tpl_vars['path']['misc']; ?>
/elfinder/css/elfinder.css" type="text/css" media="screen" charset="utf-8">

	<script src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>

	<script src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/elfinder/js/elfinder.full.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $this->_tpl_vars['path']['misc']; ?>
/elfinder/js/i18n/elfinder.ru.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" charset="utf-8">
    <?php echo '
        $(function() {

            var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\\d+).*$/, "$1");
            var langCode = window.location.search.replace(/^.*langCode=([a-z]{2}).*$/, "$1");

            $(\'#finder\').elfinder({
                url : \'/?route=elfinder&connector=1\',
                lang : langCode,
                editorCallback : function(url) {
                    if ( funcNum ) {
                        window.opener.CKEDITOR.tools.callFunction(funcNum, url);
                        window.close();
                    }
                }
            })

        });
    '; ?>

	</script>

</head>
<body>
	<div id="finder">finder</div>
</body>
</html>