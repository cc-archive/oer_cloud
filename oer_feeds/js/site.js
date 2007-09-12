function confirmDelete(form_id) {

	form = getElement(form_id);

	// we don't want to do anything here if the action wasn't "delete"
	if ( form.feedAction[form.feedAction.selectedIndex].value == "delete" ) {
		msg = "Are you sure that you want to delete the selected feed?";
		if ( confirm(msg) ) {
			return true;
		} else {
			return false;
		}
	}

}
