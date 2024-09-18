let params = e.params;
let data = params.data;


$.ajax({
    method: 'GET',
    url: lookupStockGreigeUrl,
    data: {
        wo_id: data.id
    },
    success: function (response) {
        $('#stock-greige-container').show();
        $('#StockGreigeGrid').empty();

        // Append new data to StockGreigeGrid
        $('#StockGreigeGrid').append(response);
    }
});
