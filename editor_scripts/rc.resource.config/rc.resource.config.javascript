class ResourceConfig extends Tag {
  set resource(r) {
    
    this._resource = r
    if (r) {
      let restypeData = Resource.getResource('resource_type')
      let resourceTypes = restypeData.data
      this.restypes = resourceTypes
      this.resourceType = this.getResourceType(r.type)
      this.resourceTypeName = this.resourceType.configuration
    } else {
      this.resourceTypeName = null
      this.update('this.resourceTypeName')
    }
    this.update('this.restypes')
    this.update('this.resource')
    this.update('this.resourceTypeName')
    this.update('this.resourceType')
    
  }
  get resource() {
    return this._resource
  }
  configurationUpdated(c){
    resource.action('update-config',{configuration:c,resource:this.resource.name})
  }
  getResourceType(n) {
      let idx = this.restypes.findIndex( resourceType => {  
          return (resourceType.name == n)
      } )
      return this.restypes[idx]
  }
}