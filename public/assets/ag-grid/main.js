  
 
function onFirstDataRendered(params) {
    params.api.sizeColumnsToFit();
  }
     
  function getServerSideDatasource(gridOptions,table) {
  
    const datasource =  {
         getRows(params) {
         
            
         $.ajax({
               type:'POST',
               data:JSON.stringify(params.request),
               dataType: "json",
               contentType: "application/json",
               url:gridUrl +'?table='+table,
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function(response){
                  if(response){
                    console.log("response",response.campaigns)
                    params.success({
                     rowData: response.rows,
                     rowCount: response.lastRow,
                     campaigns: response.campaigns,
                   });
                    
                    if(response.FilteredRow || role === 'agent'){
                      $("#lead-count").text(response.FilteredRow);
                    }else{
                      $("#lead-count").text(response.totalRows);
                    }
                  }else{
                     params.fail();
                  }
                  
                },error: function(e) {
                   params.fail(); 
                },
            }); 
  
        }
    };
  
     console.log("datasource",datasource)
    gridOptions.api.setServerSideDatasource(datasource);
  } 
  
  
  
  
  
   
  // setup the grid after the page has finished loading
  