/**
 * Main meadows javascript
 *
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

/**
 * The main meadows javascript class.
 * @class Meadows
 */
var meadows = (function Meadows(){

    var self = this,
        mapAddEntry;

    /**
     * Main()
     * @return {Meadows}
     */
    this.init = function meadowsInit(){

        var _self = self;

        window.setTimeout(function(){
          jQuery("#messages").fadeTo(10000, 0).slideUp(10000, function(){
            jQuery(this).remove();
          })
        })

        jQuery('#search_expr').autocomplete({
          serviceUrl: 'ajax.php?format=json',
          formatResult: function(suggestion, currentValue){
            return suggestion.title;
          },
          onSelect: function(suggestion){

            var cat = jQuery('#searchCategory').val();
            jQuery('#search_button').data('id', suggestion.id)
              .click();
          }
        });

        jQuery('#overlay').on('click', function meadowsOverlayClick(){
            jQuery('#overlay').animate({
                height: 'toggle',
                opacity: 'toggle'
            }, 'slow');
        });

        jQuery('#addEntryModal').on('shown.bs.modal', function(){
          var self = _self;

          var pickerStart = new Pikaday({ field: jQuery('.datetimepicker')[0]}),
            pickerEnd = new Pikaday({ field: jQuery('.datetimepicker')[1]});
          google.maps.event.trigger(self.mapAddEntry, 'resize');
          var myLatLng = new google.maps.LatLng(52.94050311454136,-1.1407756805419922);
          _self.mapAddEntry.setCenter(myLatLng);
        })
        google.maps.event.addDomListener(window, 'load', self.mapInit);

        return _self;
    }

    /**
     * Init for add entry google map modal dialog
     * @return {Meadows}
     */
    this.mapInit = function meadowsMapInit(){

        var _self = self;

        //vars
        var myLatLng = new google.maps.LatLng(52.94050311454136,-1.1407756805419922),
          myOptions = {
            zoom: 14,
            center: myLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          };

        _self.mapAddEntry = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        _self.marker = new google.maps.Marker({
          map: _self.mapAddEntry,
          position: myLatLng
        });

        google.maps.event.addListener(_self.mapAddEntry, 'click', function(event){

          var self = _self;

          var location = event.latLng,
            lat1 = location.lat(),
            lon1 = location.lng();

          self.marker.setMap(null);
          self.marker = new google.maps.Marker({
            map: _self.mapAddEntry,
            position: location
          })

          document.getElementById("latitude").value = lat1;
          document.getElementById("longitude").value = lon1;
        });

        _self.mapAddEntry.setCenter(_self.marker.getPosition());

        //hack click first timeline item to link with map.
        jQuery('.flag')[0].click();

        return _self;
    }

    /**
     * Constructor.
     * @class {Meadows}
     * @constructor
     */
    jQuery(document).ready(function meadowsConstruct(){
        var _self = self;

        _self.init();

        return _self;
    })

}());
