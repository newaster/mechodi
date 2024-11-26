$(function () {
    $('.js-basic-example').DataTable();

    //Exportable table
    $('.js-exportable').DataTable({
        "bFilter": false,
        "bInfo": false,
        "lengthChange": false,
        "pagingType": "simple"
//        dom: 'Bfrtip',
        
//        buttons: [
//            'copy', 'csv', 'excel', 'pdf', 'print'
//        ]
    });
});