<?php

return _document(
    _html(
        _head(
            _meta(['charset' => 'utf-8']),
            _title(
                _block('meta_title', fn() => _text('TODO Title')),
            )
        ),
        _body(
            _block('body_content', fn() => _text('TODO Content')),
        ),
    ),
);