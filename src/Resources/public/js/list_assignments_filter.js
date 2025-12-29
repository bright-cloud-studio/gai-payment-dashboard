$(document).ready(function() {

    $('select[name="filter_district"]').on('change', function() {
        filterWorkAssignments();
    });
    
    $('select[name="filter_school"]').on('change', function() {
        filterWorkAssignments();
    });
    
    $('select[name="filter_student"]').on('change', function() {
        filterWorkAssignments();
    });

});


function filterWorkAssignments() {

    var district = $('#filter_district').val();
    var school = $('#filter_school').val();
    var student = $('#filter_student').val();
    
    $( ".list_assignment_wrapper" ).each(function( index ) {
        
        // HOW FILTERING WORKS BY LOGIC
        // 1. Start off with the assumption you will show the entry
        // 2. Check if we should be filtering, and if so, if the entry matches the criteria we are filtering for
        // 3. This way if any check fails then hide the element, otherwise it is safe to show.
        
        var show = true;
        
        // if filtering by district AND the district data doesnt match our selection then flag to hide
        if(district != null && $(this).data('district') != district) { show = false; }
        
        // if filtering by school AND the school data doesnt match our selection then flag to hide
        if(school != null && $(this).data('school') != school) { show = false; }
        
        // if filtering by student AND the student data doesnt match our selection then flag to hide
        if(student != null && $(this).data('student') != student) { show = false; }
        
        // show if show, hide if not
        if(show)
            $(this).fadeIn();
        else
            $(this).fadeOut();
            
    });

}

// Reset filters
function resetFilter() {
    $('#filter_district').val(0);
    $('#filter_school').val(0);
    $('#filter_student').val(0);
    filterWorkAssignments();
}
