;(function(global, $) {
'use strict';

if (typeof global.ccmVatcodeCodicefiscale !== 'undefined') {
    return;
}

const TYPE = Object.freeze({
    VATCODE: 'vatCode',
    CODICEFISCALE: 'codiceFiscale'
});

const MAP_CODICEFISCALE = Object.freeze({
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
});

const RX_VATCODE_PATTERN = '(IT)?[0-9]{11}';
const RX_CODICEFISCALE_PATTERN = '[A-Z]{6}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{3}[A-Z]{1}';

const RX_VATCODE = new RegExp('^' + RX_VATCODE_PATTERN + '$');
const RX_CODICEFISCALE = new RegExp('^' + RX_CODICEFISCALE_PATTERN + '$');

/**
 * @param {string|any} value
 *
 * @returns {string}
 */
function normalize(value)
{
    return typeof value === 'string' ? value.toUpperCase().replace(/[^A-Z0-9]+/g, '') : '';
}

/**
 * @param {string|any} value
 * @param {boolean|undefined} normalizedValue
 *
 * @returns {boolean}
 */
function isVatCode(value, normalizedValue)
{
    if (normalizedValue) {
        value = normalize(value);
    }
    if (typeof value !== 'string' || !RX_VATCODE.test(value)) {
        return false;
    }
    if (value.startsWith('IT')) {
        value = value.substring(2);
    }
    let sum = 0;
    for (let i = 0; i <= 9; i += 2) {
        sum += parseInt(value.charAt(i), 10);
    }
    for (let i = 1; i <= 9; i += 2) {
        let c = 2 * parseInt(value.charAt(i), 10);
        if (c > 9) {
            c -= 9;
        }
        sum += c;
    }
    const checkCode = (10 - (sum % 10)) % 10;
    return checkCode === parseInt(value.charAt(10), 10);
}

/**
 * @param {string|any} value
 * @param {boolean|undefined} normalizedValue
 *
 * @returns {boolean}
 */
function isCodiceFiscale(value, normalizedValue)
{
    if (normalizedValue) {
        value = normalize(value);
    }
    if (typeof value !== 'string' || !RX_CODICEFISCALE.test(value)) {
        return false;
    }
    let sum = 0;
    for (let i = 1; i <= 13; i += 2) {
        const char = value.charCodeAt(i);
        if (char >= '0'.charCodeAt(0) && char <= '9'.charCodeAt(0)) {
            sum += char - '0'.charCodeAt(0);
        } else {
            sum += char - 'A'.charCodeAt(0);
        }
    }
    for (let i = 0; i <= 14; i += 2) {
        const char = value.charAt(i);
        sum += MAP_CODICEFISCALE[char];
    }
    const checkChar = String.fromCharCode(sum % 26 + 'A'.charCodeAt(0));
    return value.charAt(15) === checkChar;
}

/**
 * @param {string|any} value
 * @param {boolean|undefined} normalizedValue
 *
 * @returns {'vatCode'|'codiceFiscale'|''}
 * @see TYPE
 */
function getType(value, normalizedValue)
{
    if (normalizedValue) {
        value = normalize(value);
    }
    if (isVatCode(value, false)) {
        return TYPE.VATCODE;
    }
    if (isCodiceFiscale(value, false)) {
        return TYPE.CODICEFISCALE;
    }
    return '';
}

const STATIC_STUFF = Object.freeze({
    TYPE_VATCODE: {
        value: TYPE.VATCODE,
        writable: false
    },
    TYPE_CODICEFISCALE: {
        value: TYPE.CODICEFISCALE,
        writable: false
    },
    normalize: {
        value: normalize,
        writable: false
    },
    isVatCode: {
        value: isVatCode,
        writable: false
    },
    isCodiceFiscale: {
        value: isCodiceFiscale,
        writable: false
    },
    getType: {
        value: getType,
        writable: false
    },
});

const DEFAULT_OPTIONS = Object.freeze({
    /* Can be empty (any type), or $.fn.vatcodeCodicefiscale.TYPE_VATCODE ('vatCode'), or $.fn.vatcodeCodicefiscale.CODICEFISCALE ('codiceFiscale') */
    type: '',
    allowInvalidValues: false,
    normalizeOn: 'blur change',
    checkOn: 'blur keydown keypress keyup change click',
    onCheck: null
});

/**
 * @constructor
 * @param {HTMLElement} el
 * @param {Object|undefined} options
 */
function VatcodeCodicefiscale(el, options)
{
    if (!(el instanceof HTMLElement)) {
        throw new Error('VatcodeCodicefiscale constructor expects an HTMLElement as first argument');
    }
    if (el.vatcodeCodicefiscale) {
        throw new Error('VatcodeCodicefiscale constructor called on an element that already has a VatcodeCodicefiscale instance');
    }
    el.vatcodeCodicefiscale = this;
    el.dataset.vatcodeCodicefiscale = 'true';
    this.el = el;
    this.options = Object.assign({}, VatcodeCodicefiscale.defaultOptions, options || {});
    if (typeof el.dataset.vatcodeCodicefiscaleType === 'string') {
        this.options.type = el.dataset.vatcodeCodicefiscaleType;
    }
    if (!Object.values(TYPE).includes(this.options.type)) {
        this.options.type = '';
    }
    if (typeof el.dataset.vatcodeCodicefiscaleAllowInvalidValues === 'string') {
        this.options.allowInvalidValues = el.dataset.vatcodeCodicefiscaleAllowInvalidValues;
    }
    if (typeof this.options.allowInvalidValues === 'string') {
        this.options.allowInvalidValues = this.options.allowInvalidValues.trim().toLowerCase();
    }
    if ([false, 0, '0', 'off', 'no', 'n'].includes(this.options.allowInvalidValues)) {
        this.options.allowInvalidValues = false;
    } else if ([true, 1, '1', 'on', 'yes', 'y'].includes(this.options.allowInvalidValues)) {
        this.options.allowInvalidValues = true;
    } else {
        this.options.allowInvalidValues = !!this.options.allowInvalidValues;
    }
    if (typeof el.dataset.vatcodeCodicefiscaleNormalizeOn === 'string') {
        this.options.normalizeOn = el.dataset.vatcodeCodicefiscaleNormalizeOn;
    }
    if (typeof this.options.normalizeOn === 'string') {
        this.options.normalizeOn.trim().split(/\s+/).forEach((normalizeOn) => {
            if (normalizeOn === '') {
                return;
            }
            this.el.addEventListener(normalizeOn, () => {
                this.normalize();
            });
        });
    }
    if (typeof el.dataset.vatcodeCodicefiscaleCheckOn === 'string') {
        this.options.checkOn = el.dataset.vatcodeCodicefiscaleCheckOn;
    }
    if (typeof this.options.checkOn === 'string') {
        this.options.checkOn.trim().split(/\s+/).forEach((checkOn) => {
            if (checkOn === '') {
                return;
            }
            this.el.addEventListener(checkOn, () => {
                if (typeof this.options.onCheck !== 'function') {
                    return;
                }
                const normalizedValue = normalize(this.el.value);
                if (normalizedValue === '') {
                    this.options.onCheck(this.el, null);
                } else {
                    const type = getType(normalizedValue);
                    if (type === '') {
                        this.options.onCheck(this.el, false);
                    } else if (this.options.type !== '' && type !== this.options.type) {
                        this.options.onCheck(this.el, false);
                    } else {
                        this.options.onCheck(this.el, true);
                    }
                }
            });
        });
    }
    if (!this.options.allowInvalidValues) {
        const regexes = [];
        if (this.options.type === '' || this.options.type === TYPE.VATCODE) {
            regexes.push(RX_VATCODE_PATTERN);
        }
        if (this.options.type === '' || this.options.type === TYPE.CODICEFISCALE) {
            regexes.push(RX_CODICEFISCALE_PATTERN)
        }
        switch (regexes.length) {
            case 0:
                this.el.pattern = '';
                break;
            case 1:
                this.el.pattern = regexes[0];
                break;
            default:
                this.el.pattern = '(' + regexes.join(')|(') + ')';
                break;
        }
    }
}

VatcodeCodicefiscale.prototype = {
    normalize() {
        const originalValue = this.el.value;
        const normalizedValue = normalize(originalValue);
        if (originalValue !== normalizedValue) {
            this.el.value = normalizedValue;
            this.el.dispatchEvent(new Event('input', {bubbles: true}));
            this.el.dispatchEvent(new Event('change', {bubbles: true}));
        }
    },
    getType() {
        return getType(this.el.value);
    },
};
Object.defineProperties(VatcodeCodicefiscale, STATIC_STUFF);
VatcodeCodicefiscale.defaultOptions = Object.assign({}, DEFAULT_OPTIONS);

Object.defineProperty(global, 'ccmVatcodeCodicefiscale',  {
    value: VatcodeCodicefiscale,
    writable: false
});


if (typeof $ === 'function' && typeof $.fn === 'object') {
    $.fn.vatcodeCodicefiscale = function(what) {
        if (typeof what === 'string') {
            switch (what.toLowerCase()) {
                case 'normalize':
                    return this.each(function() {
                        const $this = $(this);
                        const originalValue = $this.val();
                        const normalizedValue = normalize(originalValue);
                        if (originalValue !== normalizedValue) {
                            $this.val(normalizedValue).trigger('input').trigger('change');
                        }
                    });
                case 'gettype':
                    let typeResult = null;
                    this.each(function() {
                        typeResult = getType($(this).val());
                        return false;
                    });
                    return typeResult;
                default:
                    throw new Error('Unrecognized vatcode_codicefiscale method: ' + what);
            }
        }
        const options = Object.assign({}, $.fn.vatcodeCodicefiscale, what || {});
        return this.each(function() {
            let instance;
            try {
                instance = new VatcodeCodicefiscale(this, options);
            } catch (e) {
                console.error((e ? e.message : e) || 'Unknown error');
                return;
            }
            const onCheck = instance.options.onCheck;
            if (typeof onCheck === 'function') {
                instance.options.onCheck = function(el, isValid) {
                    onCheck($(el), isValid);
                };
            }
        });
    };
    Object.defineProperties($.fn.vatcodeCodicefiscale, STATIC_STUFF);
    $.fn.vatcodeCodicefiscale.defaults = Object.assign({}, DEFAULT_OPTIONS);
}

})(this, jQuery);
