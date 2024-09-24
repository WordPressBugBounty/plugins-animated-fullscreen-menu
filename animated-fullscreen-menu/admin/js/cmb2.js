document.addEventListener('DOMContentLoaded', function() {
    var cmb2SubmitBtn = document.getElementById('submit-cmb');
    if (cmb2SubmitBtn) {
        // Add a new button to the form
        var newBtn = document.createElement('button');
        newBtn.innerHTML = 'Preview Menu';
        newBtn.className = 'afs-menu-preview-button button button-primary';
        newBtn.type = 'button';
        newBtn.onclick = function() {

            // get current website url and add new get param
            var url = window.location.href;
            url += '&afs_preview_menu=true';
            // open new tab with the url
            window.open(url, '_blank');

        };
  
        // add the new button to the form
        cmb2SubmitBtn.parentNode.appendChild(newBtn);
    }

});