$(function(){
    /**
     * all response will be in below format
     * {
     *     success : boolean,
     *     data : {resource_object} or null,
     *     message : string,
     *     code : integer
     * }
     */

    var $loader = $('#loader');

    /**
     * reset the form and show it!
     */
    $('#btn-user-add').click(function(e){
        e.preventDefault();
        $('#user-form-data').each(function(){
            this.reset();
        });
        $('#btn-user-save').attr('data-method', 'POST');
        $('#user-modal').modal('show');
    });

    /**
     * sen GET request to display resource with specific id, and display it in modal form
     */
    $('#user-table').on('click', '.btn-user-edit', function(e){
        var $userid = $(this).attr('data-id');

        e.preventDefault();
        $loader.show();

        $.get(global.baseUrl+'admin/user/'+$userid, function(resp){
            if(resp.success){
                $('#user-form-data').each(function(){
                    this.reset();
                });

                var $user = resp.data;

                for(var a in $user){
                    $('#user_'+a).val($user[a]);
                }

                $('#btn-user-save').attr('data-method', 'PUT');
                $('#user-modal').modal('show');
            }else{
                alert(resp.message);
                if(resp.code == 401){
                    location.reload();
                }
            }

            $loader.hide();
        });
    });

    /**
     * send DELETE request to the resouce server
     */
    $('#user-table').on('click', '.btn-user-delete', function(e){
        var $userid = $(this).attr('data-id');
        e.preventDefault();

        if(confirm('Are you sure to delete this user?')){
            $loader.show();
            $.ajax({
                url    : global.baseUrl+'admin/user/'+$userid,
                method : 'DELETE',
                data   : {
                    id : $userid
                },
                success : function(resp){
                    if(resp.success){
                        $('#user-row-'+$userid).remove();
                    }else{
                        alert(resp.message);
                        if(resp.code == 401){
                            location.reload();
                        }
                    }
                    $loader.hide();
                }
            });
        }
    });

    /**
     * send POST request to save data to resource server
     * or send PUT request to update data on resource server
     * based on data-method value
     */
    $('#btn-user-save').click(function(e){
        e.preventDefault();

        var $button = $(this),
            $userdata = $('#user-form-data').serialize(),
            $method = $(this).attr('data-method'),
            $url = ($method == 'POST') ? global.baseUrl+'admin/user' : global.baseUrl+'admin/user/'+$('#user_id').val();

        $button.prop('disabled', true);
        $button.html('saving...');
        $loader.show();

        $.ajax({
            url: $url,
            data: $userdata,
            method : $method,
            success: function(resp){

                $button.prop('disabled', false);
                $button.html('save');
                $loader.hide();

                if(resp.success){

                    user = resp.data;

                    if($method == 'POST'){
                        /** append user to new row */
                        $('#user-table').append(
                            '<tr id="user-row-'+resp.data.id+'">'+
                                '<td>'+user.id+'</td>'+
                                '<td>'+user.first_name+'</td>'+
                                '<td>'+user.last_name+'</td>'+
                                '<td>'+user.email+'</td>'+
                                '<td class="text-center">'+
                                    '<a data-id="'+user.id+'" class="btn btn-xs btn-primary btn-user-edit" href="#"><i class="fa fa-edit fa-fw"></i>Edit</a>'+
                                    '<a data-id="'+user.id+'" class="btn btn-xs btn-danger btn-user-delete" href="#" style="margin-left: 5px"><i class="fa fa-times fa-fw"></i>Remove</a>'+
                                '</td>'+
                            '</tr>'
                        );
                    }else{
                        var $fields = $('#user-row-'+resp.data.id+' td');
                        $($fields[1]).html(user.first_name);
                        $($fields[2]).html(user.last_name);
                        $($fields[3]).html(user.email);
                    }

                    /** reset the form and hide modal form */
                    $('#user-form-data').each(function(){
                        this.reset();
                    });
                    $('#user-modal').modal('hide');
                }else{
                    alert(resp.message);
                    if(resp.code == 401){
                        location.reload();
                    }
                }
            }
        });
    });
});