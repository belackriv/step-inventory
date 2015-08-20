<div class="panel panel-default">
	<div class="panel-heading">
		<h1 class="panel-title">User List</h1>
	</div>
	<div class="panel-body">
		<div class="container-fluid">
			<div class="col-sm-2">
				<form id="user_search_form">
					<div class="form-group">
						 <label for="user_search_input">Username</label>
						 <div class="input-group">
						 	<input type="search" id="user_search_input" class="form-control" placeholder="Seach For Username" />
						 	<span class="input-group-btn">
								<button class="btn btn-default" type="button" id="username-search-button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
							</span>
						 </div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Username</th>
				<th>Is Active?</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
				</tr>
			</thead>
		<tbody></tbody>
	</table>
</div>