# AFD package

This package contains the basis for reading and handling AFD messages based on the SIVI standard.

## Parsers

Parsing raw data to an AFD message is currently supported for two data structures:

- SKP
  - [PDF Documentation](https://github.com/rapideinternet/afd/blob/master/docs/SKP%20toelichting%2020180710.pdf)
  - [https://www.sivi.org/standaarden/sivi-koppelingsprotocol-skp/](https://www.sivi.org/standaarden/sivi-koppelingsprotocol-skp/)
  - [https://www.manula.com/manuals/sivi/sivi-koppelingsprotocol/1.1/nl/topic/handboek-sivi-koppelingsprotocol](https://www.manula.com/manuals/sivi/sivi-koppelingsprotocol/1.1/nl/topic/handboek-sivi-koppelingsprotocol)
- EDIFACT
  - [PDF Documentation](https://github.com/rapideinternet/afd/blob/master/docs/EDIFACT-Handboek-20190801.pdf)
  - [https://www.sivi.org/standaarden/gegevensstandaard/afd-downloads/](https://www.sivi.org/standaarden/gegevensstandaard/afd-downloads/)
  

## Example usage

For an implementation of this package we suggest you take a look at the [rapideinternet/afd.laravel](https://github.com/rapideinternet/afd.laravel) package.
