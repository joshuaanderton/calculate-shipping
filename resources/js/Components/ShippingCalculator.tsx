import React, { useEffect, useState } from 'react'
import { lang as __ } from '@inertia-ui/Hooks/useLang'
import InputLabel from '@/Components/InputLabel'
import InputError from '@inertia-ui/Components/InputError'
import useRoute from '@inertia-ui/Hooks/useRoute'
import TextInput from '@inertia-ui/Components/TextInput'
import Select from '@inertia-ui/Components/Select'
import PrimaryButton from '@/Components/Buttons/PrimaryButton'
import axios from 'axios'
import ValidationErrors from '@inertia-ui/Components/ValidationErrors'

interface ShippingDimensions {
  weight: number|null
  weight_unit: string
  width: number|null
  height: number|null
  length: number|null
  length_unit: string
}

interface Props {
  onError: (errors: {[field: string]: string|string[]}) => void
  dimensions: ShippingDimensions
  fees: {label: string, amount: number}[]
  currency?: string
}

interface ShippingRate {
  id: string
  carrier: string
  service: string
  currency: string
  delivery_days: number
  rate: number
  rate_int: number
}

interface Shipment {
  id: string
  rates: ShippingRate[]
}

const countries = { CA: 'Canada', US: 'United States' }

const ShippingCalculator: React.FC<Props> = ({ onError, dimensions, fees = [], currency = 'CAD' }) => {

  const [postalCode, setPostalCode] = useState<string|null>(null),
        [country, setCountry] = useState<string|null>(null)

  const route = useRoute(),
        [shipment, setShipment] = useState<Shipment|null>(null),
        [total, setTotal] = useState<number|null>(0),
        [processing, setProcessing] = useState<boolean>(false),
        [errors, setErrors] = useState<{[key: string]: string}>({}),
        [selectedRate, setSelectedRate] = useState<string|null>(null),
        formatPrice = (amount: number) => (amount / 100).toLocaleString('en-US', { currency, style: 'currency' }),
        formatRateLabel = ({ carrier, service, rate_int, delivery_days }: ShippingRate) => [
          carrier,
          service,
          formatPrice(rate_int),
          delivery_days && '- '+__(':count days', { count: delivery_days }),
        ].filter(str => !!str).join(' ')

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault()
    setProcessing(true)
    axios.post(route('shipping.estimate'), {
      ...dimensions,
      currency,
      postal_code: postalCode,
      country,
    })
      .then(response => {
        setShipment(response.data.shipment || null)
        setSelectedRate(response.data.shipment?.rates[0]?.id || null)
        setErrors(response.data.errors || {})
      })
      .catch(() => {})
      .then(() => setProcessing(false))
  }

  useEffect(() => {

    const feesTotal = fees.map(fee => fee.amount).reduce((total, fee) => total + fee, 0),
          rateTotal = !selectedRate ? 0 : (shipment?.rates.find(rate => rate.id === selectedRate)?.rate_int || 0)

    setTotal(feesTotal + rateTotal)

  }, [fees, selectedRate])

  useEffect(() => {

    if (Object.values(errors).length === 0) return

    onError(errors)

  }, [errors])

  return (
    <div className="flex flex-col md:flex-row gap-6 lg:gap-12">
      <div className="flex-1 max-w-md">
        <form id="shipping-estimate-form" onSubmit={handleSubmit} className="space-y-6">

          <fieldset className="space-y-1">
            <InputLabel value={__('Country')} />
            <Select
              value={country || '0'}
              onChange={(event: any) => setCountry(event.currentTarget.value)}
              className="w-full !text-sm h-9 !py-0"
              required
            >
              <option disabled value="0">{__('Select a country')}</option>
              {Object.entries(countries).map(([code, label]) => (
                <option key={code} value={code}>{label}</option>
              ))}
            </Select>
            <InputError message={errors?.country} />
          </fieldset>

          <fieldset className="space-y-1">
            <InputLabel value={__('Postal Code')} />
            <TextInput
              value={postalCode || ''}
              className="w-full"
              required
              onChange={event => setPostalCode(event.currentTarget.value.toUpperCase())} />
            <InputError message={errors?.postal_code} />
          </fieldset>


          <div className="flex items-center gap-x-4">

            <ValidationErrors errors={errors} />

            <PrimaryButton
              type="submit"
              form="shipping-estimate-form"
              size="sm"
              className="ml-auto whitespace-nowrap"
              processing={processing} text={__('Retrieve Shipping Rates')} />

          </div>
        </form>
      </div>

      <div className="bg-chrome-400/60 p-4 flex-1 max-w-md">
        <fieldset className="space-y-1">
          <div className="inline-flex gap-x-1 items-center">
            <InputLabel lang="ja_shipping.select_shipping_rate" value="Select Shipping Rate" disableTranslationsEditor />
            <span>{shipment && shipment.rates.length > 0 && `(${shipment.rates.length})`}</span>
          </div>
          <Select
            value={selectedRate || '0'}
            onChange={(event: any) => setSelectedRate(event.currentTarget.value)}
            className="w-full !text-sm h-9 !py-0"
            disabled={! (shipment && shipment.rates.length > 0)}
          >
            <option value="0" disabled>
              {shipment && shipment.rates.length > 0 ? __('Select shipping rate') : __('Awaiting shipping address')}
            </option>
            {shipment && shipment.rates.length > 0 && shipment?.rates?.map((rate: ShippingRate) => (
              <option key={rate.id} value={rate.id}>{formatRateLabel(rate)}</option>
            ))}
          </Select>
        </fieldset>
        <dl className="mt-4 space-y-4 text-base font-medium">
          <div className="flex items-center justify-between">
            <dt>
              <InputLabel className="whitespace-nowrap" lang="ja_shipping.shipping_fee" value="Shipping Fee" disableTranslationsEditor />
            </dt>
            <dd className="text-sm">
              {selectedRate && shipment?.rates.find(rate => rate.id === selectedRate) ? (
                <>{formatPrice(shipment.rates.find(rate => rate.id === selectedRate)!.rate_int)}</>
              ) : (
                <span className="italic site-text-muted">{__('None selected')}</span>
              )}
            </dd>
          </div>
          {fees.map(({ label, amount }) => (
            <div key={label} className="flex items-center justify-between">
              <dt className="font-medium text-sm">{label}</dt>
              <dd className="text-sm">{formatPrice(amount)}</dd>
            </div>
          ))}
          <div className="flex items-center justify-between border-t site-border pt-4">
            <dt>
              <InputLabel className="whitespace-nowrap [&_h5]:!font-semibold" lang="shops.total" value="Total" disableTranslationsEditor />
            </dt>
            <dd className="text-sm font-semibold">
              {formatPrice(total || 0)}
            </dd>
          </div>
        </dl>
      </div>

    </div>
  )
}

export default ShippingCalculator
