@extends('layouts.app')
@section('content')
<style type="text/css">
.tagify{    width: 100%;max-width: 700px;}
</style> 
<div class="row">
    <div class="col-lg-12 margin-tb">

        <div class="action-top float-end mb-3"> 
            <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> <i class="bi bi-skip-backward-circle me-1"></i> Back</a>
        </div>
         
    </div>
</div>
@if (count($errors) > 0)
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
       @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
       @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-1"></i>
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
 
@endif
<div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit User </h5>

{!! Form::model($user, ['class' =>'row g-3','method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
            
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text" id="inputGroupPrepend2">Name</span>
                    {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                 </div>
                  
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Email</span>
                       {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                   </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Password</span>
                       {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                   </div>
                </div>
               <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">C.Password</span>
                        {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                   </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Roles</span>
                        <select class="form-control" name="roles[]">
                            @foreach($roles as $role)
                            <option  

                                @if($userRole == $role)
                                selected 
                                @endif

                             value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                         
                   </div>
                </div>

                        @php
                          $agentId =  (!empty($user->agents_ids)) ? $user->agents_ids : '';
                          $agent =  ($agentId) ? json_decode($agentId) : ''; 
                          $agentStr = ($agent) ? implode("\n", $agent) : '';  
                          $agentStrjs = ($agent) ? implode(",", $agent) : '';  
                        @endphp
                          <div class="col-lg-6  d-flex justify-content-around {{  $agentId }}" >

                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="all_agent_access" id="all_agent_access" value="all"  @if($agentId =='all')  checked @endif>
                              <label class="form-check-label" for="flexSwitchCheckDefault">All Agents</label>
                            </div>
 
                </div> 

                
                <div class="row g-3" >
                    <div class="col-md-5">
                    <div class="input-group"> 
                       {!! 

                           Form::textarea('agents_ids_text', $agentStr, 
                           [
                           'class'=>'form-control',
                           'placeholder' => 'Add Agent Ids' ,
                           'id'=>"agents_ids",
                           'value'=> '' , 


                           ] ) !!}
                   </div>
                  </div>
                    <div class="col-md-5">
                     <input type="text" name='agents_ids' id="basic" value="" />
                    </div>
                    <div class="col-md-2"> 
                     <button  class="btn btn-primary float-right" id="click" type="button">Add Agents</button>
                    </div>
                </div>
                 <div class="col-12">
                  <button class="btn btn-primary" type="submit">Submit form</button>
                </div>
                </div>

              
                
               
              {!! Form::close() !!}

 
 </div>
          </div>
</div>
 
@endsection



@section('page-script') 
<script type="text/javascript"> 
    var agent = "{{ $agentStrjs }}" 
    $("#basic").val(agent)  
    var input = document.querySelector('input[name=agents_ids]');
    var tagify = new Tagify(input);
     $("#click").on("click", function() { 
        var tags = [];
        var textArea = $("#agents_ids").val(); 
        var strArray = textArea.split("\n");
        strArray.map(function(value,key){
             tags.push(value);
        }); 
        tagify.addTags(tags); 
    }); 

    $(document).on("click",'.tagify__tag__removeBtn',function(){
         var tags = [];
         var val = $(this).parent().attr('value');
        var textArea = $("#agents_ids").val(); 
        var strArray = textArea.split("\n");
        var arr = strArray.filter(function(item) {
              return item !== val
        })
        var join = arr.join('\n'); 
        $("#agents_ids").val(join)  

    })  
</script>
@endsection