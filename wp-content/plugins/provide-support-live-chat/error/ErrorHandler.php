<?php
/*
 * ErrorHandler.php
 * 
 * Copyright 2014 Ortus IT <contact@ortusit.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * Обработка 
 */

class OrtusErrorHandler
{
    
    public function __construct($errorTypes = NULL, $total_info = array(), $level = 1)
    {
        $this->total_info = $total_info;
        $this->level      = $level;
        
        if (is_null($errorTypes)) {
            $errorTypes = E_ALL | E_STRICT;
        }
        
        // регистрация ошибок
        set_error_handler(array(
            $this,
            'OtherErrorCatcher'
        ), $errorTypes);
        
        // перехват критических ошибок
        register_shutdown_function(array(
            $this,
            'FatalErrorCatcher'
        ));
        
        // создание буфера вывода
        @ob_start();
       
    }
    
    
    public function OtherErrorCatcher($errno, $errstr, $errfile, $errline, $errcontext)
    {
        echo '<br>Ошибка-warning<br>';
        echo date("y.m.d H:m:s") . "\t" . $errno . "\t" . $errstr . "\t" . $errfile . "\t" . $errline . "\t" . $errcontext;
        return false;
    }
    
    public function FatalErrorCatcher()
    {
		$error = error_get_last();
		
        if (isset($error)) {
            if ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_RECOVERABLE_ERROR)) //E_ERROR | E_PARSE | E_COMPILE_ERROR
                {
                $stack = ob_get_contents();
                @ob_end_clean(); // сбросить буфер, завершить работу буфера
                
                $this->error = array(
                    'Fatal_Error_Info' => $error,
                    'Line_Error_Info' => $this->getPartofLineError($error['file'], $error['line']),
                    'Stack' => $stack
                );
                if($_SERVER["SCRIPT_NAME"] == "/wp-admin/plugins.php"){
					$this->getPluginHtml();
				}else{
					$this->getHtml();
				}
                
            } else {
                @ob_end_flush();
            }
        } else {
            @ob_end_flush(); // вывод буфера, завершить работу буфера
        }
        
        $this->endErrorCatcher();
    }
    
    public function endErrorCatcher()
    {
        @ob_end_flush(); // вывод буфера, завершить работу буфера
        restore_error_handler();
        return true;
    }
    
    public function getPartofLineError($file = NULL, $startLine = 0)
    {
        if (!is_null($file)) {
            $f               = fopen($file, 'r');
            $lineNo          = 0;
            $startLineBefore = $startLine - 3;
            $endLine         = $startLine + 2;
            $content         = '';
            while ($line = fgets($f)) {
                $lineNo++;
                if ($lineNo >= $startLineBefore) {
                    if ($lineNo == $startLine - 1) {
                        $content .= $line . '/* in that line FATAL ERROR!!! */';
                    } else {
                        $content .= $line;
                    }
                }
                if ($lineNo == $endLine) {
                    break;
                }
            }
            fclose($f);
            return $content;
        }
    }
    
    public function getHtml()
    {
		
		$InfoController = new OrtusStruct(true, $this->total_info, $this->level, $this->error);
        $all_data       = $InfoController->viewInfo();
	
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"  type="text/javascript"></script>
	<!-- <script src='https://javascriptbase64.googlecode.com/files/base64.js' type='text/javascript'></script> -->
 
	<style type="text/css">
	body {
    min-width: 1200px;
	}
	body {
		background-color: #E6EDF8;
		font-family: Verdana,sans-serif;
		line-height: 1;
		margin: 0 auto;
		min-width: 767px;
		overflow-x: hidden;
	}
	body {
		line-height: 1;
	}
	
	.site-header{
		background-color:#fff;
		border-top: 1px solid #CDDAE9;
		height: 110px;
		margin: 15px auto 0;
		max-width: 970px;
		min-width: 970px;
		text-align:center;
	}
	article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
		display: block;
	}
	
	.site-logo {
		background: url("http://www.providesupport.com/s/gry9s1/root/images/logo.png") repeat scroll 0 0 rgba(0, 0, 0, 0);
		display: block;
		height: 38px;
		margin: 1.93em auto;
		width: 257px;
	}
	
	.article {
		background: none repeat scroll 0 0 #FFFFFF;
		box-shadow: 0 1px 0 0 #D4DFEC;
		cursor: default;
		margin: 0 auto;
		padding: 15px 35px 40px 45px;
		width: 890px;
	}
	.site-line {
		background: #5bb5e9; /* Old browsers */
		background: -moz-linear-gradient(top,  #5bb5e9 0%, #60abf8 44%, #499ee1 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#5bb5e9), color-stop(44%,#60abf8), color-stop(100%,#499ee1)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #5bb5e9 0%,#60abf8 44%,#499ee1 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #5bb5e9 0%,#60abf8 44%,#499ee1 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #5bb5e9 0%,#60abf8 44%,#499ee1 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #5bb5e9 0%,#60abf8 44%,#499ee1 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#5bb5e9', endColorstr='#499ee1',GradientType=0 ); /* IE6-9 */
		margin: 0 auto;
		padding: 20px 0 20px 0;
		width: 80%;
		max-width: 970px;
		min-width: 970px;
		text-align:center;
		
	}
	.site-line h3{
		color:#fff;
		font-size:18px;
	}

	.header {
		color: #FF310F;
		font-weight: normal;
		padding: 15px 0 10px;
	}

	.article p, .article i, .article em, .article strong, .article ul, .article ol {
		font-size: 13px;
		line-height: 18px;
		padding: 10px 0;
	}
	.header h3{
		border: 0 none;
		font: inherit;
		margin: 0;
		padding: 0;
		vertical-align: baseline;
		text-align:center;
		font-size: 14px;
	}

	textarea{
		width:100%;
		}
		
	.submit-button {
		background-color: #4AA0E2;
		border-radius: 2px;
		border-width: 0;
		color: #FFFFFF;
		cursor: pointer;
		font-size: 16px;
		height: 21px;
		margin: auto;
		min-width: 70px;
		padding: 20px;
		text-align: center;
		width: 280px;
	}
	
	.submit-button:hover{
		opacity: 0.9;
	}
	.msgs{
		text-align:center; 
		padding: 10px;
		font-size: 14px;
	}
	.sending{
		background-color: #26BA12;
		color: #FFFFFF;
		display: none;
		font-family: inherit;
		font-size: 15px;
		line-height: 20px;
		margin: 10px auto;
		padding: 10px;
		text-align: center;
	}
	
	</style>
	<script type="text/javascript">
	jQuery(document).ready(function(){
	
	sending = function(){
		
		var ajaxData = {
			//logwordpress: Base64.encode(JSON.stringify(jQuery('#total_info').html()))
			//logwordpress: Base64.encode(jQuery('#total_info').html());
			logwordpress: "<?php echo base64_encode(json_encode($all_data)); ?>"
		};
		
		jQuery.ajax({
			type: 'POST',
			url: 'http://errorreport.providesupport.com/cgi-bin/providesupport/logwordpress.cgi',
			data: ajaxData
			}).always(function(){
				jQuery('.msgs').hide();
				jQuery('.sending').fadeIn();
				  
			});
		}
	});
	</script>
</head>

<body>
	<header role="banner" class="site-header">
		<div id="provide-logo">
			<a title="Provide Support Live Chat software tool for website owners" href="http://www.providesupport.com/" class="logo-link">
				<div class="site-logo"></div>
			</a>
			<div class="cleared"></div>
	</div>
	</header>
	<div class="site-line">
		<h3>Critical error(s) found in CMS module(s). Debug information is available below:</h3>
	</div>
	<article class="article">
		<section>
		<header class="header">
			<h3>You can copy (press CTRL+A to select the log and CTRL+C to copy it) this log to send it to your Wordpress admin or supporting service if needed.</h3>
		</header>
		<br />
		<textarea name="" id="total_info" cols="" rows="30">
		<?php
        print_r($all_data);
?>
		</textarea>
		<div class="msgs">I understand and agree that the report can contain information about my server.</div>
		<div id="send" class="submit-button">SEND REPORT to Provide Support</div>
		<div class="sending">REPORT was sent to Provide Support.<br />Thank you!</div>
		</section>
	</article>
	<?php
        $ActionInfoController = new OrtusAction($all_data);
        
        if (!$ActionInfoController->stateSend) {
            echo '<script type="text/javascript">jQuery("document").ready(function(){
            jQuery("#send").on("click", function(){
				sending();
				jQuery(this).fadeOut();
            });
			});</script>';
        }
?>	
</body>
</html>
<?php
    }


public function getPluginHtml()
    {
		$InfoController = new OrtusStruct(true, $this->total_info, $this->level, $this->error);
        $all_data       = $InfoController->viewInfo();
        $ActionInfoController = new OrtusAction($all_data);
    }

}
