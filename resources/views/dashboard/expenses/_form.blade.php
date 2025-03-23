@csrf
        @if(isset($service->id))
            @method('PUT')
        @endif
        
        <!-- Service Information -->
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <x-form.input label='Service Name:' type='text' name='service_name' :value='$service->service_name' placeholder='Enter Service Name'/>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <x-form.textarea label='Description:' name='description' :value='$service->description' placeholder="Enter service description"/>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <x-form.input label='Service Price:' type='number' name='service_price' :value='$service->service_price' placeholder='Enter Service Price'/>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <x-form.input label='Duration (minutes):' type='number' name='duration' :value='$service->duration' placeholder='Enter Service duration'/>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group status-section">
                    <label>Service Status:</label>
                    <select name="is_active" class="form-control">
                        <option value="1" @selected(old('is_active', $service->is_active) == '1')>Active</option>
                        <option value="0" @selected(old('is_active', $service->is_active) == '0')>Not Active</option>
                    </select>
                    @error('is_active')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            

        </div>