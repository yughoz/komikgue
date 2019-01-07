<!DOCTYPE html>
<html>
<head>
	<title>Finger Test</title>
</head>
<body>
	<form class="form-horizontal">
		<fieldset>

		<!-- Form Name -->
		<legend>Form Name</legend>

		<!-- Select Basic -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="device">Device</label>
		  <div class="col-md-4">
		    <select id="device" name="device" class="form-control">
		      <option >HP</option>
		      <option >Laptop</option>
		    </select>
		  </div>
		</div>
		<br>
		<!-- Select Basic -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="browser">Browser</label>
		  <div class="col-md-4">
		    <select id="browser" name="browser" class="form-control">
		      <option >Chrome</option>
		      <option >Mozila</option>
		      <option >IE/edge</option>
		      <option >Safari</option>
		    </select>
		  </div>
		</div>

		<br>
		<!-- Text input-->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="username">Username / email</label>  
		  <div class="col-md-4">
		  <input id="username" name="username" type="text" placeholder="" class="form-control input-md">
		  <span class="help-block"></span>  
		  </div>
		</div>
		  <input id="finger" name="finger" type="hidden" placeholder="" class="form-control input-md">

		<br>
		<br>
		<!-- Button -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="singlebutton"></label>
		  <div class="col-md-4">
		    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Save</button>
		  </div>
		</div>
		<br>

		</fieldset>
		</form>
</body>
    <script src="{{ asset('js/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('js/finger/fingerprint.js') }}"></script>
    <script type="text/javascript">
	  $(document).ready(function(){
	  	var fingerprint = new Fingerprint().get();
	  	$('#finger').val(fingerprint);
	  	$('form').on('submit', function(){
	  		$("#wait").html("Please wait ....");
            $.ajax({
              type: 'post',
              headers: {
		            'X-CSRF-TOKEN': "{{ csrf_token() }}"
		        },
              // {{ csrf_token() }}
              dataType: 'json',
              url: "{{ url('') }}/Finger/add",
				data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false

              success:function(response){
                try {
	  				$("#wait").html("");
						parseData = response;
						if(parseData['status'] == "success"){
							window.open(parseData.data.link,'_blank');
							$("#link").attr("href", parseData.data.link);
							$("#link").html("link");
							// alert(parseData.data.link)
							// location.reload();
							// self.clearData();
						} else{
							alert("error")
							// $('#createMainModal').modal('show');
						}
					} catch(e) {
						console.log(e);
						alert("error")
					}
              }
            });

		});
	  });
    	
    </script>
</html>