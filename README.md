# Persistent DataObjects - Experimental / Work in Progress

Persistent and optionally immutable & versioned DataObjects for SilverStripe

The two major features of this module are:

1. **A DataObject subclass that can not be deleted**    
   Calling `->delete()` will mark an object as deleted, but not actually delete it    
   *(Where necessary, objects can still be deleted by calling `->purge()`)*
1. **A DataObjectExtension that adds versioning**    
   In contrast to the "silverstripe-versioned" module, versioning is achived by 
   making DataObjects immutable and overloading `->write()` to create a duplicate 
   of the current record instead of saving the existing.        
   This means the `ID` becomes the unique version number, while an additional 
   `VersionGroupID` and `VersionGroupLatest` keep track of the relation of records. 
   The benefit of this approach is being able to easily reference a version of an
   entry rather than always the latest version. Thus making it possible to have 
   persistent storage of information that is easily integrable in other parts of 
   SilverStripe (eg an invoice can safely reference a product price and does not 
   need to create a snapshot).

## TODOs / Planed features

- [ ] Tests
- [ ] Revisit decision to put VersionGroup_ID on DataObject rather than subclasses
- [ ] Extend GridField integration
   - [ ] Hide/Show deleted records button
   - [ ] History view to access older version from within a DataObject
- [ ] Implement non GridField form fields (eg Relation Dropdown that let's the user pick a entry and a version)
- [ ] Implement Database Field/Relation that references VersionGroupID instead of ID?
- [ ] Documentation
   - [ ] Explain the usecase in more detail
   - [ ] Examples
- [ ] SilverStripe 4 Support
