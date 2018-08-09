Summary: NethServer NUT configuration
Name: nethserver-nut
Version: 1.3.2
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

Requires: nethserver-base
Requires: nut

BuildRequires: perl
BuildRequires: nethserver-devtools 

%description
NethServer UPS managemnt using NUT

%prep
%setup

%build
%{makedocs}
perl createlinks

%install
rm -rf %{buildroot}
(cd root; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} > %{name}-%{version}-filelist


%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update
%doc COPYING
%ghost %attr(0644, root, root) %{_sysconfdir}/collectd.d/nut_nethserver.conf

%changelog
* Thu Aug 09 2018 Davide Principi <davide.principi@nethesis.it> - 1.3.2-1
- Enhancement: (un)mask password fields - NethServer/dev#5554

* Tue May 16 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.1-1
- NUT UPS master unreachable from clients - Bug NethServer/dev#5282

* Thu Jul 21 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.3.0-1
- collectd - monitor only locally connected ups - NethServer/dev#5049

* Fri Jul 08 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.2.1-1
- Fix .spec typo

* Thu Jul 07 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.2.0-1
- First NS7 release

* Fri May 27 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.12-1
- Dashboard/UPS: no info for some UPS models - Enhancement #3397 [NethServer]

* Tue Sep 29 2015 Davide Principi <davide.principi@nethesis.it> - 1.0.11-1
- Make Italian language pack optional - Enhancement #3265 [NethServer]
- nethserver-devbox replacements - Feature #3009 [NethServer]

* Wed Oct 15 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.10-1.ns6
- Fix dashboard error when configured as slave - Bug #2842

* Tue Jul 08 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.8-1.ns6
- Fix web interface: device not saved - Bug #2800

* Wed Feb 26 2014 Davide Principi <davide.principi@nethesis.it> - 1.0.7-1.ns6
- Revamp web UI style - Enhancement #2656 [NethServer]

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.0.6-1.ns6
- Move admin user in LDAP DB - Feature #2492 [NethServer]
- NUT: add option to enable mail notification - Enhancement #2291 [NethServer]
- Update all inline help documentation - Task #1780 [NethServer]

* Wed Oct 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.5-1.ns6
- Upsd daemon should listen on 0.0.0.0 - Enhancement #2139 
- Db defaults: remove Runlevels prop. Refs #2067

* Tue Jul 23 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.4-1.ns6
- Auto-generate password on first install #1760
- Added a simple form for driver suggestion during UPS configuration #1760

* Tue Jul 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1.ns6
- Bump release #1760 [NethServer]

* Tue Jul 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1.ns6
- web ui: remove php warning on temperature parameter. #1760

* Tue Jul 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1.ns6
- Fix warnings on Dashboard when no UPS is connected #1760
- Fix upsd.conf template

* Fri Jun 07 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1.ns6 
- First release
