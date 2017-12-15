(function(doc) {
    const statusField = doc.getElementById('search_data_status');

    statusField.addEventListener('change', function() {
        this.form.submit();
    });
})(document);
