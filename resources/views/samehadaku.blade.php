<!DOCTYPE html>
<html>
<head>
	<title>Samehadaku Crack</title>
</head>
<body>
	<h1>SAMEHADAKU CRACK</h1>
	<h4>paste link greget.space , tetew.info ,siotong dll</h4>
	<form action="javascript:void(0)">
		<input type="text" name="url" id="url">
		<input type="hidden" name="source" id="source" value="js">
		<input type="submit" name="">
	</form>
	<div id="wait"></div>
	<a id="link" href="#" target="_blank"></a>
</body>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- samehadaku -->
	<ins class="adsbygoogle"
		 style="display:block"
		 data-ad-client="ca-pub-9523665357221895"
		 data-ad-slot="4596975581"
		 data-ad-format="auto"
		 data-full-width-responsive="true"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
    <script src="{{ asset('js/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript">
	  $(document).ready(function(){
	  	$('form').on('submit', function(){
	  		$("#wait").html("Please wait ....");
            $.ajax({
              type: 'post',
              headers: {
		            'X-CSRF-TOKEN': "{{ csrf_token() }}"
		        },
              // {{ csrf_token() }}
              dataType: 'json',
              url: "{{ url('') }}/Samehada/download",
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