$(function(){
    $('#checkConnection').click(function(e){
        e.preventDefault();
        var $this = $(this),
            $dbsettings = $('#dbsettings').serialize(),
            $alert = $('#alert');

        $this.prop('disabled', true);
        $this.html('Checking Connection...');
        $alert.html('');

        $.post(global.baseUrl+'install.php/db/check', $dbsettings, function(response){

            $this.prop('disabled', false);
            $this.html('check');

            if(response.success){
                $('#saveConfiguration').prop('disabled', false);
                $alert.html(
                    '<div class="alert alert-success alert-dismissable">'+
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
                        response.message+
                    '</div>'
                );
            }else{
                $('#saveConfiguration').prop('disabled', true);
                $alert.html(
                    '<div class="alert alert-danger alert-dismissable">'+
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
                        response.message+
                    '</div>'
                );
            }
        });
    });

    $('#dbdriver').change(function(){
        var $driver = $(this).val();

        $('#dbsettings .config').each(function(){
            var $div = $(this);
            if($div.hasClass($driver)){
                $div.show();
            }else{
                $div.hide();
            }
        });
    }).trigger('change');

});