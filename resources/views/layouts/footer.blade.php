
  
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1
    </div>
    <strong>Copyright &copy; 2017 <a href="#"></a>.</strong> 
  </footer>


<div class="js-form_modal_holder"></div>
</div>
<!-- ./wrapper -->

<!-- alertify -->
<script src="{{ asset('cms/plugins/alertifyjs/alertify.min.js') }}"></script>

<!-- jQuery 2.2.3 -->
<script src="{{ asset('cms/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<script src="{{ asset('cms/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>

<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('cms/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('cms/plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('cms/dist/js/app.min.js') }}"></script>
{{--  <!-- Sparkline -->
<script src="{{ asset('cms/plugins/sparkline/jquery.sparkline.min.js') }}"></script>  --}}
{{--  <!-- jvectormap -->
<script src="{{ asset('cms/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('cms/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>  --}}
<!-- SlimScroll 1.3.0 -->
<script src="{{ asset('cms/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
{{--  <!-- ChartJS 1.0.1 -->
<script src="{{ asset('cms/plugins/chartjs/Chart.min.js') }}"></script>  --}}
<!-- jquery-toast-plugin -->
<script src="{{ asset('cms/plugins/jquery-toast-plugin/jquery.toast.min.js') }}"></script>


@yield('scripts')

<script>
    function delete_data (data)
        {
            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary btn-flat";
            alertify.defaults.theme.cancel = "btn btn-danger btn-flat";
            alertify.confirm('Confirmation', 'Are you sure you want to delete the data?', 
                function(){
                    $.ajax({
                        url         : data.url,
                        type        : 'POST',
                        data        : data.reqData,
                        dataType    : 'JSON',
                        success     : function (res) {
                            if (res.code == 1)
                            {
                                show_toast_message({heading : 'Error', icon : 'error', text : res.general_message, hideAfter : 6000 });
                            }
                            else 
                            {
                                show_toast_message({heading : 'Success', icon : 'success', text : res.general_message, hideAfter : 6000 });
                                //call_fetch_data(1);
                                if (typeof data.fetch_data.func !== "undefined" && typeof data.fetch_data.func === "function") 
                                {
                                    data.fetch_data.func(data.fetch_data.params);
                                }
                            }
                        }
                    });
                }, 
                function () {

            });
        }
        
        function fetch_data (data)
        {
            data.target.children('.overlay').removeClass('hidden');
            $.ajax({
                url : data.url,
                type : 'POST',
                data : data.formData,
                processData : false,
                contentType : false,
                success     : function (res) {
                    data.target.children('.box').children('.overlay').addClass('hidden');
                    data.target.html(res);
                }
            });
        }

        var toast = '';
        var toastSuccess = '';
        function show_toast_message (data) 
        {
            if (data.icon == 'error')
            {
                toast = $.toast({
                    heading: data.heading,
                    text: data.text,
                    showHideTransition: 'slide',
                    icon: data.icon,
                    hideAfter: data.hideAfter,
                    position: 'bottom-right',
                    loader : false,
                    stack: 2, 
                })
            }
            else
            {
                toastSuccess = $.toast({
                    heading: data.heading,
                    text: data.text,
                    showHideTransition: 'slide',
                    icon: data.icon,
                    hideAfter: data.hideAfter,
                    position: 'bottom-right',
                    loader : false,
                    stack: 2, 
                })
            }
        }

        function save_data (data) 
        {

            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary btn-flat";
            alertify.defaults.theme.cancel = "btn btn-danger btn-flat";
            alertify.confirm('Confirmation', 'Are you sure you want to save the data?', 
            function(){  
                
                var formData = new FormData(data.form[0]);
                
                data.form.parents('.box').children('.overlay').removeClass('hidden');
                $.ajax({
                    url : data.url,
                    type : 'POST',
                    data : formData,
                    processData : false,
                    contentType : false,
                    success     : function (res) {
                        
                        data.form.children('.form-group').children('.help-block').children('code').fadeOut('slow', function () {
                        
                        });
                        data.form.parents('.box').children('.overlay').addClass('hidden');
                        if (res.code == 1)
                        {
                            show_toast_message({heading : 'Error', icon : 'error', text : res.general_message, hideAfter : 6000 });
                            var text = [];
                            for(var err in res.messages)
                            {
                                $('#' + err +'-error').html('<code style="display:none">'+ res.messages[err] +'</code>');
                                $('#' + err +'-error').children('code').fadeIn('slow');
                                text.push(res.messages[err]);
                            }
                            show_toast_message({heading : 'Error', icon : 'error', text : text, hideAfter : 10000 });
                        }
                        else if (res.code == 2)
                        {
                            show_toast_message({heading : 'Error', icon : 'error', text : res.general_message, hideAfter : 6000 });
                        }
                        else 
                        {
                            show_toast_message({heading : 'Success', icon : 'success', text : res.general_message, hideAfter : 6000 });
                            $('#form_modal').modal('hide');
                            if (typeof data.fetch_data.func === "function") 
                            {
                                data.fetch_data.func(data.fetch_data.params);
                            }
                            //call_fetch_data(1);
                        }
                    }
                }); 
            }, function(){  

            });
        }
        function show_form_modal (data) 
        {

            console.log(data);
            $.ajax({
                url : data.url,
                type : 'POSt',
                data : data.reqData,
                success : function (resData) {
                    
                    data.target.html(resData);
                    data.target.children('.modal').modal({backdrop : 'static'})
                                        .on('shown.bs.modal', function () {
                                            if (typeof data.func.init_tag_input  != "undefined")
                                            {
                                                if (typeof data.func.init_tag_input  !== "undefined" && typeof data.func.init_tag_input === "function") 
                                                {
                                                    data.func.init_tag_input();
                                                }
                                            }
                                        })
                                        .on('hidden.bs.modal', function () {
                                            $('#js-modal_holder').html('');
                                            if (toast)
                                            {
                                                toast.reset();
                                            }
                                        });
                }
            });
        }
</script>
</body>
</html>