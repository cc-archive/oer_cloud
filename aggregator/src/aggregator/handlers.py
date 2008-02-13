"""Convenience wrapper for loading the available feed handlers into a 
mapping object."""

import pkg_resources

_HANDLERS = None

def get():
    """Lazily load the available handlers."""
    
    global _HANDLERS

    if _HANDLERS is None:
        _HANDLERS = {}
        for e in pkg_resources.iter_entry_points('cc.aggregator'):
            _HANDLERS[e.name] = e

    return _HANDLERS
