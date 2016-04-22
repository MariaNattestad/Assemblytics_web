<!DOCTYPE html>

<html>

<!--    NAVIGATION BAR-->
<?php include "header.html";?>
<?php include "title.html";?>

<!--INSTRUCTIONS-->

<!-- <div class="center"> -->
  <div class="row">
          <div class="col-lg-7"> 
                  <div class="panel panel-default">
                      <div class="panel-heading"> <h3 class="panel-title">Instructions</h3></div>
                      <div class="panel-body"><p>Upload a delta file to analyze alignments of an assembly to another assembly or a reference genome</p>
                        <ol>
                          <li>Download and install <a href="http://sourceforge.net/projects/mummer/files/" target="_blank">MUMmer</a>
                            </li>
                          <li>Align your assembly to a reference genome using nucmer (from MUMmer package)
                              <p><pre>$ nucmer -maxmatch -l 100 -c 500 REFERENCE.fa ASSEMBLY.fa -prefix OUT</pre></p>
                              <p>
                                Consult the <a href="http://mummer.sourceforge.net/manual/" target="_blank">MUMmer manual</a> if you encounter problems</li>
                              </p></li>
                          <li>Delta-filter to reduce file size before upload (from MUMmer package): Here the 10000 should match the "Unique sequence length required" selected on the right.
                            The minimum you can choose is 1000 which runs more slowly than 10000 especially on large genomes. Check the size of the final OUT.l10000.delta file. If the file size is larger than 500 MB, it might take a long time to run. 
                              <p><pre>$ delta-filter -l 10000 OUT.delta > OUT.l10000.delta </pre></p>
                              </li>
                          <li>Upload the output file OUT.l10000.delta (<a href="tests/Arabidopsis.l10000.delta" target="_blank">view example</a>) to Assemblytics</li>
                        </ol>
                      </div>
                  </div>
          </div>
          <div class="col-lg-5"> 
                  <div class="panel panel-default">
                          <div class="panel-heading"> <h3 class="panel-title">Run Assemblytics</h3></div>
                          <div class="panel-body">
                                  	<!--    DROPZONE   -->
                                  	<div class="center frame"> 
                                      	<form action="file_upload.php"
                                      	    class="dropzone"
                                      	    id="myAwesomeDropzone">
                                            <!-- <div class="dz-message" data-dz-message><span>Drop delta file here</span></div> -->
                                      	    <input type="hidden" name="code_hidden" value="">
                                      	</form>
                                        <!--   end of DROPZONE   -->
                                  	</div>
                                  <div class="center frame"> 
                                  <!--    SUBMIT BUTTON with hidden field to transport code to next page   -->
                                  <form name="input_code_form" action="input_validation.php"  method="post">
                                        <p>
                                          <div class="input-group input-group-lg">
                                            <span class="input-group-addon">Description</span>
                                             <input type="text" name="nickname" class="form-control" value = "my favorite organism">
                                          </div>
                                        </p>
                                        <p>
                                          <div class="input-group input-group-lg">
                                            <span class="input-group-addon">Unique sequence length required</span>
                                             <input type="number" max="100000" step="1000" min="1000" name="uniqlength" class="form-control" value = "10000">
                                          </div>
                                        </p>
                                        <p>
                                          <div class="input-group input-group-lg">
                                            <span class="input-group-addon">Minimum variant size</span>
                                             <input type="number" max="50" step="1" min="1" name="min_size" class="form-control" value = "50">
                                          </div>
                                        </p>

                                        <!-- <p>
                                          <div class="input-group input-group-lg">
                                            <span class="input-group-addon">Read length</span>
                                             <input type="number" step="1" name="read_length" class="form-control" value = "100">
                                          </div>
                                        </p> -->
                                        <p id="analysis_form">
                                      <!--  submit button set from within front_page_script.js --> 
                                        </p>
                                  </form>
                                  </div>
                          </div>
                  </div>
          </div>
  </div>
<!-- </div> -->



<!--View analysis later-->
<!-- <div id="codepanel" class="center">
    <div class="panel panel-info">
      <div class="panel-heading"><h3 class="panel-title">View analysis later</h3></div>
      <div id="code" class="panel-body">
         contents set from within front_page_script.js 
      </div>

    </div>
</div>  -->

<!--scripts at the end of the file so they don't slow down the html loading-->
<script src="js/front_page_script.js"></script>
<script src="js/dropzone.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
Dropzone.options.myAwesomeDropzone = {
  accept: function(file, done) {
    console.log("uploaded");
    done();
  },
  init: function() {
    this.on("addedfile", function() {
      if (this.files[1]!=null){
        this.removeFile(this.files[0]);
      }
    });
  }
};  

</script>
</body>
</html>
