<!DOCTYPE html>

<html>

<!--    NAVIGATION BAR-->
<?php include "header.html";?>
<?php include "title.html";?>

<!--INSTRUCTIONS-->

<div class="center">


    <div class="panel panel-info">
        <div class="panel-heading"> <h3 class="panel-title">Instructions</h3></div>
        <div class="panel-body"><p>Upload the results from running MUMmer nucmer.</p>
          <p>Instructions for running nucmer: 
          <ol>
            <li>Download and install MUMmer from:
                <a href="http://sourceforge.net/projects/mummer/files/" target="_blank">http://sourceforge.net/projects/mummer/files/</a>
            <li>Align your assembly to a reference genome using nucmer (from MUMmer package)
                <p><pre>$ nucmer -maxmatch -l 100 -c 1000 REFERENCE_GENOME.fasta ASSEMBLY.fasta -prefix ASSEMBLY.alignments</pre></p>
                <p>
                  Consult the MUMmer manual if you encounter problems: <a href="http://mummer.sourceforge.net/manual/" target="_blank">http://mummer.sourceforge.net/manual/</a></li>
                </p></li>
            <li>Upload the output file (.delta) to ABVC</li>
          </ol>
          </p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
                	<!--    DROPZONE   -->
                	<div class="center"> 
                    	<form action="file_upload.php"
                    	    class="dropzone"
                    	    id="myAwesomeDropzone">
                          <!-- <div class="dz-message" data-dz-message><span>Drop jellyfish file here or click to upload</span></div> -->
                    	    <input type="hidden" name="code_hidden" value="">
                    	</form>
                    	
                    	<!--    SUBMIT BUTTON with hidden field to transport code to next page   -->
                    	<form name="input_code_form" action="input_validation.php"  method="post">
                            
                            <p>
                              <div class="input-group input-group-lg">
                                <span class="input-group-addon">Nickname</span>
                                 <input type="text" name="nickname" class="form-control" value = "my_assembly">
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
                  <!--   end of DROPZONE   -->
        </div>
      
        <div class="col-lg-6">  
            <!--View analysis later-->
            <div id="codepanel" class="center">
              	<div class="panel panel-info">
              	  <div class="panel-heading"><h3 class="panel-title">View analysis later</h3></div>
              	  <div id="code" class="panel-body">
                    <!--  contents set from within front_page_script.js --> 
                  </div>

              	</div>
            </div>
        </div>
    </div>
</div>


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
