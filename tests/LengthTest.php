<?php

use Ja\Shipping\Support\Length;

it('converts in to cm', function () {
    expect(Length::from(5, 'in')->toCm())->toBe(12.7);
});

it('converts cm to in', function () {
    expect(Length::from(5, 'cm')->toIn())->toBe(1.9685);
});

it('converts in to in', function () {
    expect(Length::from(5, 'in')->toIn())->toBe(5.0);
});

it('converts cm to cm', function () {
    expect(Length::from(5, 'cm')->toCm())->toBe(5.0);
});

it('throws error when converting to an unknown unit', function () {
    Length::from(5, 'in')->to('blah');
})->throws('Unsupported unit "blah"');

it('throws error when converting from an unknown unit', function () {
    Length::from(5, 'blah')->to('in');
})->throws('Unsupported unit "blah"');
