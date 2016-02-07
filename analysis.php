<!DOCTYPE html>

<html>


<!--    NAVIGATION BAR-->
    <?php include "header.html";?>
    
        <!-- <div class="row"> -->

            <!--LEFT-->
            <!-- <div class="col-lg-8"> -->
                    <!-- ////////////////////////////////////////////////// -->
                    <!-- ////////////////      RESULTS     //////////////// -->
                    <!-- ////////////////////////////////////////////////// -->
                    <div class = "center" id="results">
                        <div class="thumbnail frame">
                            <div class = "caption" style="text-align: center"><h3 id="nickname_header">HEADER </h3></div>
                        </div>
                        <div class="thumbnail plot_frame frame">
                            <div style="display:inline-block" id="landing_for_plot1" class="plot_img">
                                <!-- Landing spot for plot image -->
                            </div>
                            <div style="display:inline-block" id="landing_for_plot2" class="plot_img">
                                <!-- Landing spot for plot image -->
                            </div>
                        </div>
                        <div class="thumbnail frame plot_frame">
                            <div class="caption">
                                <h4>Variant statistics</h4>
                                <p id="landing_for_plot1_details">Plot details</p>
                                <p><a href="" download class="btn btn-primary" class="download_btn" id="down_img_1" role="button">Download plot image</a>
                                    <a href="" download class="btn btn-default" class="download_btn" id="down_txt_1"  role="button">Download all variants (.bed file)</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
            <!-- </div> -->

            <!-- RIGHT-->   
            <!-- <div class="col-lg-4">   -->
                
            <!-- </div>  -->
    <!-- </div> -->

    
    <!-- </div>    end of centered middle of body -->
    <!--View analysis later-->
    <div id="codepanel" class="center">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">View analysis later</h3>
          </div>
          <div id="code" class="panel-body">
            <?php
                $code=$_GET["code"];
                $url="http://qb.cshl.edu/assemblytics/analysis.php?code=$code";
    
                echo "Return to view your results at any time: <input type=\"text\" class=\"form-control\" value=\"$url\"></input>";
            ?>
          </div>
        </div>
    </div>

    <!-- ////////////////////////////////////////////////// -->
    <!-- /////////////      Progress info     ///////////// -->
    <!-- ////////////////////////////////////////////////// -->
    <div class="panel panel-info center" id="progress_panel">
      <div class="panel-heading">
        <h3 class="panel-title">Progress</h3>
      </div>
      <div class="panel-body">
        <div id="plot_info">
        Checking progress...
        </div>
      </div>
    </div> <!-- End of progress info -->
    <!-- ////////////////////////////////////////////////// -->
    


    
<!--   jquery must be first because bootstrap depends on it   -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>


<script src="js/jquery.csv-0.71.min.js"></script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/analysis_page_script.js"></script>



</body>
</html>




