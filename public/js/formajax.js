function formAjax(edit, action) {
    

    $(".form").each(function () {
        var submitBtnHtml = $(this).find("button[type=submit]").html();
        
        var form = this;
        $(form).find("button[type=submit]").click(function(){
                if ($(form).find('input[type=file]').length > 0) {
                
                if ($(form).find('input[type=file]')[0].required && $(form).find('input[type=file]').val().length <= 0)
                    return error('please select file');
            }
        });
        
        
        this.onsubmit = function (e) {
            e.preventDefault();
            var form = this;
            console.log($(this).find("button[type=submit]"));
            $(this).find("button[type=submit]").html('<i class="fa fa-spin fa-spinner" ></i>');
            $(this).find("button[type=submit]").attr('disabled', 'disabled');
            
            
            var formdata = new FormData();
            var elements = this.elements;
            var self = this;

            for (var i = 0; i < elements.length; i++) {
                var e = elements[i];
                if (e.name.length > 0) {
                    if (e.type == "file") {
                        if (e.files[0] != undefined)
                            formdata.append(e.name, e.files[0]);
                    } else
                        formdata.append(e.name, e.value);
                }
                 
            }

            //sendPost(this.action, formdata, function(r){console.log(r);});

            $.ajax({
                url: this.action,
                type: 'POST',
                data: formdata,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                success: function (data) {
                    if (data.status == 1) {
                        success(data.message);
                        // reload data 
                        try{
                            $('#table').DataTable().ajax.reload();
                        }catch(e){}

                        if (self.action.indexOf("update") < 0 && !edit)
                            self.reset();
                    } else {
                        error(data.message);
                    }
                    
                    $(self).find("button[type=submit]").html(submitBtnHtml);
                    $(self).find("button[type=submit]").removeAttr('disabled');
            
                    
                    if (action)
                        action();
                }
            });

            return false;
        };
    });

}


