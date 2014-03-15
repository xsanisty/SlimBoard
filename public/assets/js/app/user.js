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
        });
    });

    /**
     * send DELETE request to the resouce server
     */
    $('#user-table').on('click', '.btn-user-delete', function(e){
        e.preventDefault();
        var $userid = $(this).attr('data-id');
        if(confirm('Are you sure to delete this user?')){
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

        var $button = $(this);
        var $userdata = $('#user-form-data').serialize();
        var $method = $(this).attr('data-method');
        var $url = ($method == 'POST') ? global.baseUrl+'admin/user' : global.baseUrl+'admin/user/'+$('#user_id').val();

        $button.prop('disabled', true);
        $button.html('saving...');

        $.ajax({
            url: $url,
            data: $userdata,
            method : $method,
            success: function(resp){
                if(resp.success){

                    user = resp.data;
                    $button.prop('disabled', false);
                    $button.html('save');

                    if($method == 'POST'){
                        /** append user to new row */
                        $('#user-table').append(
                            '<tr id="user-row-'+resp.data.id+'">'+
                                '<td>'+user.id+'</td>'+
                                '<td>'+user.first_name+'</td>'+
                                '<td>'+user.last_name+'</td>'+
                                '<td>'+user.email+'</td>'+
                                '<td>'+
                                    '<div class="dropdown text-center">'+
                                        '<a class="btn btn-primary btn-sm" href="#" data-toggle="dropdown">Action <b class="caret"></b></a>'+
                                        '<ul aria-labelledby="dLabel" role="menu" class="dropdown-menu pull-right">'+
                                            '<li class="text-left"><a data-id="'+user.id+'" class="btn-user-edit" href="#">Edit</a></li>'+
                                            '<li class="text-left"><a data-id="'+user.id+'" class="btn-user-delete" href="#">Remove</a></li>'+
                                        '</ul>'+
                                    '</div>'+
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