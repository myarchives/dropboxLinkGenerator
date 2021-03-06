<!DOCTYPE html>
<html>
<head>
	<title>Dropbox Link Generator</title>
	<script src="js/jquery-1.10.2.min.js"></script>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="js/jquery-ui.js"></script>
</head>

<body>
	<div class="container">
		<div class="alert alert-danger alert-dismissible" id="errorMsg"></div>
		<h2>Create Shareable Links</h2>
		<div class="row">
			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-body">
						<form id="dropboxForm">
							<div class="form-group">
								<label for="txtAPiKey">Enter your Dropbox API key</label>
								<div class="input-group">
									<input type="password" class="form-control" id="txtAPiKey" placeholder="API Key" name="txtAPiKey">
									<span class="input-group-addon" id="btnToggleAPIDisplay" style="cursor: pointer">
										<i class="glyphicon glyphicon-eye-open"></i>
									</span>
								</div>
								<label style="color:red;" id="txtAPiKeyError">Please enter API key *</label>
							</div>
							<div class="form-group">
								<label for="txtFolderPaths">Folder Path</label>
								<textarea class="form-control" id="txtFolderPaths" placeholder="Folder Path" rows="4" name="txtFolderPaths"></textarea>
								<label style="color:red;" id="folderPathError">Please enter folder name *</label>
							</div>
							<input type="submit" id="btnGenerateLinks" name="submit" class="btn btn-primary" value="Generate Links">
							<div id="dp_loader" style="display: none;">
								<img src="img/loader.gif" alt="" src="img/loader.gif" style="height:30px;">
								<label>Please wait .....</label>
							</div> 		
						</form>
					</div>
				</div>
			</div>
			<div class="col-lg-6" id="linkResult" style="display:none;">
				<div class="panel panel-success" id="successLinks" style="display:none"></div>
				<div class="panel panel-danger" id="failLinks" style="display:none;"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h1>How this works</h1>
				<p>This utility will allow a user to generate the <strong>Shareable Links</strong> in Bulk for all the files within a given folder.</p>
				<hr>
				<h3>1. Generating API Key</h3>
				<ul>
					<li>Log in to Dropbox</li>
					<li>Visit Link <a href="https://www.dropbox.com/developers/apps" target="_blank">https://www.dropbox.com/developers/apps</a></li>
					<li>Click on Create app (button to top right)</li>
					<li>Select the option <strong>"Dropbox API"</strong> (first option)</li>
					<li>Select the option <strong>"Full Dropbox"</strong></li>
					<li>Give an appropriate name to your app</li>
					<li>Once the app is created, Open it</li>
					<li>You have to click on the button to <strong>"Generate Access Token"</strong> which is present in the "OAuth2" section of the app</li>
					<li>Copy the token that is generated and save it somewhere</li>
					<li>This Token is used as the <strong>API Key</strong> in our application</li>
					<li>Dropbox API Documentaion:https://www.dropbox.com/developers/documentation/http/documentation</li>
				</ul>
				<h3>2. Getting Folder Paths</h3>
				<ul>
					<li>The folder path should be the path relative from the <strong>Dropbox Root Directory</strong></li>
					<li>If more than 1 folder paths are to be selected, then insert new folder path using <strong>comma(,)</strong></li>
					<li>
						Example : <br>
						<code>SS18/Amazon/amazon 172 lot 5,SS18/Amazon/amazon 172 lot 6,SS18/Raymond Next/raymondnext 172</code>
					</li>
					<li>Each folder path will have a corresponding csv file generated</li>
					<li>All the files in the folder as well as all the sub-folders will generate Shareable links</li>
					<li>Folder Paths are case sensitive. Please make sure you add proper casing and spaces while adding folder paths.</li>
				</ul>
				<h3>Privacy Policy</h3>
				<p>We do not store your dropbox API token with us. The token always remains in your browser storage and we use it only when we need to generate the Shareable links.<br>
				We do share any of the Shareable links generated by us to others unless they have the unique token shared with you.<br>
				The unique token shared with you will also be stored in the browser storage and will only be used when we need to check the status of the job.</p>
			</div>
		</div>
		<hr>
		<footer class="text-center">&copy; Dropbox Shareable Links</footer>
	</div>

	<script>
		$(document).ready(function () {
			$("#txtAPiKeyError").hide();
			$("#folderPathError").hide();
			$("#errorMsg").hide();
			$(document).on('click','#btnToggleAPIDisplay',function () {
				var inputVal = $("#txtAPiKey").prop('type');
				if(inputVal == 'password'){
					// Show API key
					$("#btnToggleAPIDisplay").html('<i class="glyphicon glyphicon-eye-close"></i>');
					$("#txtAPiKey").attr('type', 'text');
				} else {
					// Hide API key
					$("#btnToggleAPIDisplay").html('<i class="glyphicon glyphicon-eye-open"></i>');
					$("#txtAPiKey").attr('type', 'password');
				}
			});
			$('#txtAPiKey').keyup(function(){
				if($(this).val().length !=0)
					$('#txtAPiKeyError').hide();        
				else
					$('#txtAPiKeyError').show();
			});
			$('#txtFolderPaths').keyup(function(){
				if($(this).val().length !=0)
					$('#folderPathError').hide();        
				else
					$('#folderPathError').show();
			});
			$('#btnGenerateLinks').on('click', function(e){
				//Stop the form from submitting itself to the server.
				e.preventDefault();
				var apiKey = $("#txtAPiKey").val();
				if(apiKey == ""){
					$("#txtAPiKeyError").show();
					return false;
				}else{
					$("#txtAPiKeyError").hide();
					var folders = $("#txtFolderPaths").val();
					if(folders == ""){
						$("#folderPathError").show();
						return false;
					}else{
						$("#btnGenerateLinks").hide();
						$("#dp_loader").show();
						$("#folderPathError").hide();
						$.ajax({
							url: 'dropboxLinkGenerator.php',
							type: 'POST',
							data: ({
								api_key : apiKey,
								folders : folders
							}),
							success: function (data) {
								console.log(data);
								$("#btnGenerateLinks").show();
								$("#dp_loader").hide();
								var data = $.parseJSON(data);
								if($.isArray(data)){
									$("#btnGenerateLinks").show();
									$("#dp_loader").hide();
									var fileListSuccess = '';
									var fileListFail = '';
									$.each(data, function (index, item) {
										switch(item.Status){
											case 1 :
												fileListSuccess += '<li><a href="'+item.File_path+'"><i class="fa fa-check"></i> ' +item.Folder+ '</a></li>';
											break;
											case 0 :
												fileListFail += '<li style="color:red"><i class="fa fa-exclamation-circle"></i> ' + item.Folder + ' (Error): '+item.Msg+ '</li>';
											break;
										} 
									});
									$('#linkResult').show();
									if(fileListSuccess != ''){
										$("#successLinks").show();
										$("#successLinks").html("<div class='panel-body'><b>Download CSV File for below folder(s):</b><ul style='list-style:none;'>" + fileListSuccess + "</ul></div>");
									}
									if(fileListFail != ''){
										$('#failLinks').show();
										$("#failLinks").html("<div class='panel-body'><b>Unable to create Shareable links for below folder(s):</b><ul style='list-style:none;'>" + fileListFail + "</ul></div>");
									}
								}else{
									var res = data.hasOwnProperty('ErrorMsg');
									console.log(res);
									if(res == true){
										$('#errorMsg').show();
										$('#errorMsg').html(' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+data.ErrorMsg +'.')
									}
								}
							}
						}); 
					}
				}
			});
		});
	</script>
</body>
</html>
