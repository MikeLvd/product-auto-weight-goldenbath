jQuery(document).ready(function ($) {

    // Function to set the volume based on the dimensions
    function set_vol($this) {
        var parent = $this.parents('.dimensions_field');
        var x = 1;
        var c = 0;
        parent.find('input').each(function () {
            var v = parseFloat($(this).val());
            if (!isNaN(v) && v > 0) {
                x = x * v;
                c++;
            }
        });
        var divisor = autoWeightSettings.divisor ? parseFloat(autoWeightSettings.divisor) : 5000;
        if (x > 1 && c === 3) {
            var result = (x / divisor).toFixed(1);
            var weightField = parent.prev('.form-field').find('input.short');
            weightField.val(result);
            var currentValue = weightField.val();
            weightField.val(currentValue.replace('.', ','));
        }
    }

    // Event listener for keyup on dimension fields to update volume
    $(document).on('keyup', '.dimensions_field input', debounce(function (e) {
        set_vol($(this));
    }, 100));

    // Event listener for change on dimension fields to update volume
    $(document).on('change', '.dimensions_field input', function (e) {
        set_vol($(this));
    });

    // Debounce function to delay the execution of functions
    function debounce(func, wait) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                func.apply(context, args);
            }, wait);
        };
    }

    // Event listener for the publish button
    $('#publish').on('click', function(e) {
        if (autoWeightSettings.enableFieldsCheck === 'yes') {
            var isVariableProduct = $('select#product-type').val() === 'variable';
            var allFieldsFilled = true;

            // Get the dimensions and weight of the parent product
            var parentLength = $('input[name="_length"]').val().trim();
            var parentWidth  = $('input[name="_width"]').val().trim();
            var parentHeight = $('input[name="_height"]').val().trim();
            var parentWeight = $('input[name="_weight"]').val().trim();

            // If it's a variable product and parent dimensions are not filled, check variations
            if (isVariableProduct && (!parentLength || !parentWidth || !parentHeight || !parentWeight)) {
                var variationHasDimensions = false;
                $('.woocommerce_variation').each(function() {
                    var length = $(this).find('input[name^="variable_length"]').val().trim();
                    var width  = $(this).find('input[name^="variable_width"]').val().trim();
                    var height = $(this).find('input[name^="variable_height"]').val().trim();
                    var weight = $(this).find('input[name^="variable_weight"]').val().trim();

                    // If any variation has dimensions filled, set the flag and break
                    if (length && width && height && weight) {
                        variationHasDimensions = true;
                        return false; // Break out of the .each loop
                    }
                });
                if (!variationHasDimensions) {
                    allFieldsFilled = false;
                }
            } else if (!parentLength || !parentWidth || !parentHeight || !parentWeight) {
                allFieldsFilled = false;
            }

            // If not all fields are filled, prevent publishing and alert the user
            if (!allFieldsFilled) {
                e.preventDefault();
                alert('Εισαγάγετε τις διαστάσεις και το βάρος πριν δημοσιεύσετε το προϊόν. Ελέγξτε εαν το βασικό προιον η οι παραλλαγές του διαθέτουν διαστάσεις και βάρος');
            }
        }
    });
});