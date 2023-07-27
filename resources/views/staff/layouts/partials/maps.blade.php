<div class="modal fade" id="maps-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pick Location</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
           
                <div class="modal-body">
                   <p> Search or Select a location!</p>
                    <input id="pac-input" class="form-control" type="text" placeholder="Search Box">
                    <!--map div-->
                    <div id="map" class="gmaps"></div>
                    <input type="text" id="lat" readonly="yes"> : <input type="text" id="lng" readonly="yes">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-round pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-round">save</button>
                </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>