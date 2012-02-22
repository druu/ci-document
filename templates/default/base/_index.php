
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>#@#_TPL_TITLE#@#</title>

    #@#_TPL_HEAD#@#
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">#@#BRAND#@#</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>CI-DOCUMENT!</h1>
        <p>This is a template-engine based on marker replacement. I don't really know what to say, so best have a look at the code and usage information to get the stuff working!</p>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span12">
#@#CONTENT_VIEW#@#

#@#CONTENT#@#
        </div>
      </div>

      <hr>

      <footer>
        <p>#@#COPYRIGHT#@# - #@#FOOTNOTE#@#</p>
      </footer>

    </div> <!-- /container -->
	

  </body>
</html>
