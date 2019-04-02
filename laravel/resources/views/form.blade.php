@extends('layouts.master')

@section('content')
<div id='new'>
        <p>
            <label for="product_name">Product Name</label>
            <input type="text" value="<?php echo !empty($form['product_name']) ? $form['product_name'] : ''; ?>" name="product_name" />
        </p>
        <p>
            <label for="quantity">Quantity in stock</label>
            <input type="text" value="<?php echo !empty($form['quantity']) ? $form['quantity'] : ''; ?>" name="quantity" />
        </p>
        <p>
            <label for="item_price">Price per item</label>
            <input type="text" value="<?php echo !empty($form['item_price']) ? $form['item_price'] : ''; ?>" name="item_price" />
        </p>
        <p>
            <input type="submit" id="submit_new" value="Submit" />
        </p>
</div>
<hr />
<div id="error_section">
</div>
<div id="data_list">
</div>
@stop

@section('js-footer')
<script type="text/javascript">
    var _token = "{{ csrf_token() }}";
    $(document).ready(function() {
        $.ajax({
            url : "{{ url('/form/list') }}",
            method : "get",
            dataType : "json",
            success : function(response) {
                if(response.success)
                    fillList(response.data);
                else
                    fillErrorList(response.errors);
            },
            error : function(a, b, c) {
                console.log(a, b, c);
            }
        });
        $("#submit_new").click(saveObj);
    });

    function editObj() {
        var div = $(this).parent().parent();
        div.find("input[name='product_name']").attr('disabled', false);
        div.find("input[name='quantity']").attr('disabled', false).change();
        div.find("input[name='item_price']").attr('disabled', false).change();
        $(this).html("Save").click(saveObj);
    };

    function saveObj() {
        new_item = ($(this).attr('id') == 'submit_new');
        var div = $(this).parent().parent();
        var data = {};
        data.product_name = div.find("input[name='product_name']").first().val();
        data.quantity = div.find("input[name='quantity']").first().val();
        data.item_price = div.find("input[name='item_price']").first().val();
        if(!new_item)
            data.id = div.find("input[name='id']").first().val();
        else
            div.find("input[type='text']").val("");
        data._token = _token;
        var method = "post";
        if(!new_item)
            method = "put";
        $.ajax({
            url : "{{ url('/form') }}",
            method : method,
            data : data,
            dataType : "json",
            success : function(response) {
                if(response.success)
                    fillList(response.data)
                else
                    fillErrorList(response.errors);
            },
            error : function(a, b, c) {
                console.log(a, b, c);
            }
        });
    }

    function deleteObj() {
        var confirm = confirm("Are you sure you want to delete this raw?");
        if(confirm) {
            var div = $(this).parent().parent();
            $.ajax({
                url : "{{ url('/form') }}",
                method : "delete",
                data : { 'id' : div.find("input[name='id']").first().val() },
                dataType : 'json',
                success : function(response) {
                    if(response.success)
                        fillList(response.data);
                    else
                        fillErrorList(response.errors);
                },
                error : function(a, b, c) {
                    console.log(a, b, c);
                }
            });
        }
    };

    function fillList(data) {
        $('#data_list').html("");
        for(var i in data) {
            var div = $('<div></div>');
            var p = $('<p></p>');
            var edit_button = $('<button>Edit</button>');
            edit_button.click(editObj);
            p.append(edit_button);
            var delete_button = $('<button>Delete</button>');
            delete_button.click(delete_button);
            p.append(delete_button);
            div.append(p);
            p = $('<p></p>');
            fillLabel(p, 'product_name', 'Product name');
            fillInput(p, 'product_name', data[i]);
            div.append(p);
            p = $('<p></p>');
            fillLabel(p, 'quantity', 'Quantity in stock');
            fillInput(p, 'quantity', data[i]);
            div.append(p);
            p = $('<p></p>');
            fillLabel(p, 'item_price', 'Price per item');
            fillInput(p, 'item_price', data[i]);
            div.append(p);
            p = $('<p></p>');
            fillLabel(p, 'total_value_number', 'Total Value');
            fillInput(p, 'total_value_number', data[i]);
            div.append(p);
            fillInput(div, 'id', data[i], true);
            div.append('<br />');
            $('#data_list').append(div);
        }
    }

    function fillLabel(obj, name, title) {
        obj.append("<label for='" + name + "'>" + title + "</label>");
    }
    
    function fillInput(obj, name, data, hidden) {
        hidden = hidden || false;
        var input = $('<input />');
        input.attr('name', name);
        input.val(data[name]);
        input.attr('disabled', true);
        if(hidden)
            input.attr('hidden', true);
        obj.append(input);
    }

    function fillErrorList(data) {
        console.log(data);
        $('#error_section').html("");
        for(var i in data) {
            var p = $('<p></p>');
            p.css('color', 'red');
            fillLabel(p, 'error_'+i, data[i]);
            $('#error_section').append(p);
        }
    }

</script>
@stop