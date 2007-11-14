<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->escape($this->title) ?></title>
    <!-- EXT JS -->
    <link rel="stylesheet" type="text/css" href="extjs/resources/css/ext-all.css" />
    <link rel="stylesheet" type="text/css" href="extjs/resources/css/xtheme-gray.css" />
	
    <script type="text/javascript" src="extjs/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="extjs/ext-all-debug.js"></script>

    <!-- eGW -->
    <link rel="stylesheet" type="text/css" href="Egwbase/css/egwbase.css"/>
    <script type="text/javascript" language="javascript" src="Egwbase/js/Egwbase.js"></script>
    <script type="text/javascript" language="javascript">
            <?php
                foreach ((array)$this->configData as $index => $value) {
                    echo "Egw.Egwbase.Registry.add('$index'," . Zend_Json::encode($value) . ");console.log('add $index');\n";
                }
            ?>
    </script>
    <?php 
    	foreach ($this->jsIncludeFiles as $name) {
    		echo "\n    ". '<script type="text/javascript" language="javascript" src="'. $name .'"></script>';
    	}
    	foreach ($this->cssIncludeFiles as $name) {
    		echo "\n    ". '<link rel="stylesheet" type="text/css" href="'. $name .'" />';
    	}
    ?>
    
    <script type="text/javascript" language="javascript">
        <?php
           foreach ($this->initialData as $appname => $data) {
               if (!empty($data) ) {
                   foreach ($data as $var => $content) {
                       echo "Egw.$appname.$var = ". Zend_Json::encode($content). ';';
                   }
               }
           }
        ?>

        Ext.onReady(function(){
            Egw.Egwbase.initFramework();
            Egw.Egwbase.MainScreen.display();
            window.focus();
    	});
    </script>
</head>
<body>
</body>
</html>
