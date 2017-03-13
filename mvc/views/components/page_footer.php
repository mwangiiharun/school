        </div><!-- ./wrapper -->


        <!-- Bootstrap js -->
        <script type="text/javascript" src="<?php echo base_url('assets/bootstrap/bootstrap.min.js'); ?>"></script>
        <!-- Style js -->
        <script type="text/javascript" src="<?php echo base_url('assets/inilabs/style.js'); ?>"></script>

        <!-- Jquery datatable tools js -->
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/jquery.dataTables.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/dataTables.buttons.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/jszip.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/pdfmake.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/vfs_fonts.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/buttons.html5.min.js'); ?>"></script>
        <!-- dataTables Tools / -->
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/dataTables.bootstrap.js'); ?>"></script>

        <script type="text/javascript" src="<?php echo base_url('assets/inilabs/inilabs.js'); ?>"></script>

        <script src="<?=base_url('assets/highcharts/highcharts.js')?>"></script>
        <script src="<?=base_url('assets/highcharts/highcharts-more.js')?>"></script>
        <script src="<?=base_url('assets/highcharts/data.js')?>"></script>
        <script src="<?=base_url('assets/highcharts/drilldown.js')?>"></script>
        <script src="<?=base_url('assets/highcharts/exporting.js')?>"></script>


        <!-- Jquery gritter -->
        <!-- datatable with buttons -->
        <script>
        $(document).ready(function() {
          $('#example3, #example1, #example2').DataTable( {
              dom: 'Bfrtip',
              buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdfHtml5'
              ],
              search: false
          } );
        } );
        </script>
        <!-- dataTable with buttons end -->

        <script type="text/javascript">
            $(function() {
                $("#withoutBtn").dataTable();
            });


        </script>

        <?php if ($this->session->flashdata('success')): ?>
            <script type="text/javascript">
                toastr["success"]("<?=$this->session->flashdata('success');?>")
                toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "500",
                  "hideDuration": "500",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
            </script>
        <?php endif ?>
        <?php if ($this->session->flashdata('error')): ?>
           <script type="text/javascript">
                toastr["error"]("<?=$this->session->flashdata('error');?>")
                toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "500",
                  "hideDuration": "500",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
            </script>
        <?php endif ?>

        <?php
            if(isset($footerassets)) {
                foreach ($footerassets as $assetstype => $footerasset) {
                    if($assetstype == 'css') {
                      if(count($footerasset)) {
                        foreach ($footerasset as $keycss => $css) {
                          echo '<link rel="stylesheet" href="'.base_url($css).'">'."\n";
                        }
                      }
                    } elseif($assetstype == 'js') {
                      if(count($footerasset)) {
                        foreach ($footerasset as $keyjs => $js) {
                          echo '<script type="text/javascript" src="'.base_url($js).'"></script>'."\n";
                        }
                      }
                    }
                }
            }
        ?>
        <script type="text/javascript">
            // Set active state on menu element
            var current_url = window.location.href;
    		    var base_url = '<?=base_url()?>';

            $("ul.sidebar-menu li a").each(function(index, value) {

              if (current_url == $(this).attr('href')) {
                $(this).parents('li').addClass('active');
              } else if (current_url.startsWith($(this).attr('href'))) {
              	$(this).parents('li').addClass('active');
              } else if ($(this).attr('href').startsWith(current_url)) {
              	$(this).parents('li').addClass('active');
              }

        			if(current_url.startsWith(base_url+'markpercentage') && index == 21) {
        				$(this).parents('li').removeClass('active');
        			} else if(current_url.startsWith(base_url+'examschedule') && index == 16) {
                $(this).parents('li').removeClass('active');
              }  else if(current_url.startsWith(base_url+'mark') && index == 21) {
                $(this).child('li').removeClass('active');
              } else if(current_url.startsWith(base_url+'exam') && index == 16) {
                $(this).child('li').removeClass('active');
              } else if((current_url == (base_url+'user')) && index == 59) {
                $(this).parents('li').removeClass('active');
              } else if((current_url == (base_url+'usertype')) && index == 4) {
                $(this).parents('li').removeClass('active');
              }
              
            });
        </script>
    </body>
</html>
