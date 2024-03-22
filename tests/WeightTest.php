<?php

use Ja\Shipping\Support\Weight;

it('converts lb to kg', function () {
    expect(Weight::from(5, 'lb')->toKg())->toBe(2.26796);
});

it('converts kg to lb', function () {
    expect(Weight::from(5, 'kg')->toLb())->toBe(11.02312);
});

it('converts lb to oz', function () {
    expect(Weight::from(5, 'lb')->toOz())->toBe(80.0);
});

it('converts oz to lb', function () {
    expect(Weight::from(5, 'oz')->toLb())->toBe(0.3125);
});

it('converts kg to oz', function () {
    expect(Weight::from(5, 'kg')->toOz())->toBe(176.36995);
});

it('converts oz to kg', function () {
    expect(Weight::from(5, 'oz')->toKg())->toBe(0.14175);
});

it('converts lb to lb', function () {
    expect(Weight::from(5, 'lb')->toLb())->toBe(5.0);
});

it('converts oz to oz', function () {
    expect(Weight::from(5, 'oz')->toOz())->toBe(5.0);
});

it('converts kg to kg', function () {
    expect(Weight::from(5, 'kg')->toKg())->toBe(5.0);
});

it('throws error when converting to an unknown unit', function () {
    Weight::from(5, 'lb')->to('blah');
})->throws('Unsupported unit "blah"');

it('throws error when converting from an unknown unit', function () {
    Weight::from(5, 'blah')->to('lb');
})->throws('Unsupported unit "blah"');
