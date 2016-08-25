
<html>

	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=GBK">
	</head>
	<title>error</title>
<body>



<?=$style?>

	<div class="container">


		<div class="details-container cf">

			<header>
				<div class="exception">
					<h3 class="exc-title">
                    	\Core\Handler
						<span class="exc-title-primary">Handler</span>
					</h3>
              		<p class="exc-message">
                    	<?=$e['message']?>
					</p>
            	</div>
          	</header>
			<div class="frame-code-container ">
				<div class="frame-code active" id="frame-code-0">
					<div class="frame-file">
						<a href="subl://open?url=file://E%3A%5Cweb%5Cwwwroot%5Claravels.com%5Capp%5Cconfig%5Capp.php&amp;line=16" class="editor-link">
                        	<span class="editor-link-callout">open:</span>
							<strong>
								<?=$e['file']?> [<?=$e['line']?>]
							</strong>
                      	</a>
					</div>
                <pre class="code-block prettyprint linenums:9 prettyprinted"><ol class="linenums"><?php

						foreach($code as $k=>$v){
							$class = '';
							if($k == $e['line']){
								$class = ' current active';
							}
							if(  $k == ($e['line']-1) ||  $k == ($e['line']+1) ){
								$class = ' current';
							}
							?><li value="<?=$k?>" class="L0 <?=$class?>"><?=htmlentities($v)?></li><?php
						}

						?></ol></pre>

					<div class="frame-comments empty">
					</div>
                </div>
                <div class="frame-code" id="frame-code-1">
                	<div class="frame-file">
                    	<strong>&lt;#unknown&gt;</strong>
                    </div>
					<div class="frame-comments empty">
					</div>
                </div>
            </div>

        	<div class="details">
            	<div class="data-table-container" id="data-tables">
                	<div class="data-table" id="sg-serverrequest-data">
                  		<label>Server/Request Data</label>
                        <table class="data-table">
							<thead>
							  <tr>
								<td class="data-table-k">Key</td>
								<td class="data-table-v">Value</td>
							  </tr>
							</thead>
                            <tbody>
							<?php foreach($_SERVER as $k=>$v){ ?>
								<tr>
								  <td><?=$k?></td>
								  <td><?=$v?></td>
                        		</tr>
							<?php } ?>
                            </tbody>
						</table>
                    </div>
                    <div class="data-table" id="sg-get-data">
						<label>GET Data</label>
                        <span class="empty">empty</span>
                    </div>

                </div>

            	<div class="data-table-container" id="handlers">
              		<label>Registered Handlers</label>
                    <div class="handler active">

					</div>
                </div>

          	</div> <!-- .details -->
        </div>


    </div>

<?=$jquery?>
<?=$prettify?>
    <script>
      $(function() {
        prettyPrint();

        var $frameLines  = $('[id^="frame-line-"]');
        var $activeLine  = $('.frames-container .active');
        var $activeFrame = $('.active[id^="frame-code-"]').show();
        var $container   = $('.details-container');
        var headerHeight = $('header').css('height');

        var highlightCurrentLine = function() {
          // Highlight the active and neighboring lines for this frame:
          var activeLineNumber = +($activeLine.find('.frame-line').text());
          var $lines           = $activeFrame.find('.linenums li');
          var firstLine        = +($lines.first().val());

          $($lines[activeLineNumber - firstLine - 1]).addClass('current');
          $($lines[activeLineNumber - firstLine]).addClass('current active');
          $($lines[activeLineNumber - firstLine + 1]).addClass('current');
        };

        // Highlight the active for the first frame:
        highlightCurrentLine();

        $frameLines.click(function() {
          var $this  = $(this);
          var id     = /frame\-line\-([\d]*)/.exec($this.attr('id'))[1];
          var $codeFrame = $('#frame-code-' + id);

          if($codeFrame) {
            $activeLine.removeClass('active');
            $activeFrame.removeClass('active');

            $this.addClass('active');
            $codeFrame.addClass('active');

            $activeLine  = $this;
            $activeFrame = $codeFrame;

            highlightCurrentLine();

            $container.animate({ scrollTop: headerHeight }, "fast");
          }
        });
      });
    </script>


</body></html>
