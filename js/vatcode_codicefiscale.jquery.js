/* jshint unused:vars, undef:true, browser:true, jquery:true */

;(function(global, $) {
'use strict';

var TYPE = {
    VATCODE: 'vatCode',
    CODICEFISCALE: 'codiceFiscale'
};

var MAP_CODICEFISCALE = {
    '0': 1,
    '1': 0,
    '2': 5,
    '3': 7,
    '4': 9,
    '5': 13,
    '6': 15,
    '7': 17,
    '8': 19,
    '9': 21,
    'A': 1,
    'B': 0,
    'C': 5,
    'D': 7,
    'E': 9,
    'F': 13,
    'G': 15,
    'H': 17,
    'I': 19,
    'J': 21,
    'K': 2,
    'L': 4,
    'M': 18,
    'N': 20,
    'O': 11,
    'P': 3,
    'Q': 6,
    'R': 8,
    'S': 12,
    'T': 14,
    'U': 16,
    'V': 10,
    'W': 22,
    'X': 25,
    'Y': 24,
    'Z': 23
};

function normalize(value) {
    if (typeof value !== 'string') {
        return '';
    }
    return value.toUpperCase().replace(/[^A-Z0-9]+/g, '');
}

function getType(value)
{
    if (isVatCode(value)) {
        return TYPE.VATCODE;
    }
    if (isCodiceFiscale(value)) {
        return TYPE.CODICEFISCALE;
    }
    return '';
}

function isVatCode(value)
{
    if (typeof value !== 'string' || !/^[0-9]{11}$/.test(value)) {
        return false;
    }
    var sum = 0, i, c;
    for (i = 0; i <= 9; i += 2) {
        sum += parseInt(value.charAt(i), 10);
    }
    for (i = 1; i <= 9; i += 2) {
        c = 2 * parseInt(value.charAt(i), 10);
        if (c > 9) {
            c -= 9;
        }
        sum += c;
    }
    var checkCode = (10 - (sum % 10)) % 10;
    return checkCode === parseInt(value.charAt(10), 10);
}

function isCodiceFiscale(value)
{
    if (typeof value !== 'string' || !/^[A-Z]{6}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{3}[A-Z]{1}$/.test(value)) {
        return false;
    }
    var sum = 0, i, char;
    for (i = 1; i <= 13; i += 2) {
        char = value.charCodeAt(i);
        if (char >= '0'.charCodeAt(0) && char <= '9'.charCodeAt(0)) {
            sum += char - '0'.charCodeAt(0);
        } else {
            sum += char - 'A'.charCodeAt(0);
        }
    }
    for (i = 0; i <= 14; i += 2) {
        char = value.charAt(i);
        sum += MAP_CODICEFISCALE[char];
    }
    var checkChar = String.fromCharCode(sum % 26 + 'A'.charCodeAt(0));
    return value.charAt(15) === checkChar;
}

$.fn.vatcodeCodicefiscale = function(what) {
    if (what === 'normalize') {
        return this.each(function() {
            var $this = $(this),
                originalValue = $this.val(),
                normalizedValue = normalize(originalValue);
            if (originalValue !== normalizedValue) {
                $this.val(normalizedValue);
            }
        });
    }
    var options = $.extend($.fn.vatcodeCodicefiscale.defaults, what || {});
    if (!('type' in options) || (options.type !== TYPE.VATCODE && options.type !== TYPE.CODICEFISCALE)) {
        options.type = '';
    }
    return this.each(function() {
        var $this = $(this);
        if ($this.data('vatcodeCodicefiscale')) {
            return;
        }
        var type = $this.data('vatcode-codicefiscale-type');
        if (type !== '' && type !== TYPE.VATCODE && type !== TYPE.CODICEFISCALE) {
            $this.data('vatcode-codicefiscale-type', options.type);
        }
        if (options.normalizeOn) {
            $this.on(options.normalizeOn, function() {
                var originalValue = $this.val(),
                    normalizedValue = normalize(originalValue);
                if (originalValue !== normalizedValue) {
                    $this.val(normalizedValue);
                }
            });
        }
        if (options.checkOn) {
            $this.on(options.checkOn, function() {
                if (options.onCheck) {
                    var normalizedValue = normalize($this.val());
                    if (normalizedValue === '') {
                        options.onCheck($this, null);
                    } else {
                        var type = getType(normalizedValue);
                        if (type === '') {
                            options.onCheck($this, false);
                        } else {
                            var wantedType = $this.data('vatcode-codicefiscale-type');
                            if (wantedType !== '' && type !== wantedType) {
                                options.onCheck($this, false);
                            } else {
                                options.onCheck($this, true);
                            }
                        }
                    }
                }
            });
        }
        $this.data('vatcodeCodicefiscale', true);
    });
};

try {
    Object.defineProperty($.fn.vatcodeCodicefiscale, 'TYPE_VATCODE', {
        value: TYPE.VATCODE,
        writable: false
    });
} catch (e) {
    $.fn.vatcodeCodicefiscale.TYPE_VATCODE = TYPE.VATCODE;
}
try {
    Object.defineProperty($.fn.vatcodeCodicefiscale, 'TYPE_CODICEFISCALE', {
        value: TYPE.CODICEFISCALE,
        writable: false
    });
} catch (e) {
    $.fn.vatcodeCodicefiscale.TYPE_CODICEFISCALE = TYPE.CODICEFISCALE;
}

$.fn.vatcodeCodicefiscale.defaults = {
    /* Can be empty (any type), or $.fn.vatcodeCodicefiscale.TYPE_VATCODE ('vatCode'), or $.fn.vatcodeCodicefiscale.CODICEFISCALE ('codiceFiscale') */
    type: '',
    normalizeOn: 'blur change',
    checkOn: 'blur keydown keypress keyup change click',
    onCheck: null
};

    
})(this, jQuery);
