import React, { useEffect, useRef } from 'react';
import {
  Animated,
  Dimensions,
  Text,
  View
} from 'react-native';
import BottomNavbar from '../components/BottomNavbar';
import { MapStyles as styles } from '../constants/theme';

const { width, height } = Dimensions.get('window');

const MapScreen = () => {
  const fadeAnim = useRef(new Animated.Value(0)).current;      
  const slideUpAnim = useRef(new Animated.Value(30)).current; 

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, { toValue: 1, duration: 600, useNativeDriver: true }),
      Animated.spring(slideUpAnim, { toValue: 0, friction: 6, useNativeDriver: true }),
    ]).start();
  }, []);

  return (
    <View style={styles.container}>
      <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>

        {/* --- MAP BACKGROUND LAYER --- */}
        <View style={styles.mapContainer}>
          {/* Curved Roads (Simulated with large border radius views) */}
          <View style={[styles.road, { top: 100, left: 60, width: 2, height: height }]} />
          <View style={[styles.roadCurve, { top: 200, left: -200, width: 800, height: 400, borderRadius: 400, borderTopWidth: 8 }]} />
          <View style={[styles.roadCurve, { top: 500, left: -100, width: 900, height: 500, borderRadius: 450, borderTopWidth: 8, transform: [{ rotate: '15deg' }] }]} />

          <View style={styles.dangerZoneCircle} />

          <View style={styles.craterDashedRing} />

          <MapPin x="22%" y="34%" color="#22C55E" label="Shelter" />
          <MapPin x="74%" y="28%" color="#22C55E" label="Shelter" />
          <MapPin x="57%" y="49%" color="#EF4444" label="Crater" dotSize={14} />
          <MapPin x="40%" y="61%" color="#0096FF" label="You" />
          <MapPin x="65%" y="71%" color="#FBBF24" label="Ash" />
        </View>

        {/* --- TOP SEARCH BAR ---
        <SafeAreaView style={styles.topOverlay}>
          <View style={styles.searchBar}>
            <Feather name="navigation-2" size={18} color="#0096FF" style={{ transform: [{ rotate: '45deg' }] }} />
            <TextInput
              placeholder="Search evacuation routes..."
              placeholderTextColor="#94A3B8"
              style={styles.searchInput}
            />
            <TouchableOpacity style={styles.layersBtn}>
              <Feather name="layers" size={18} color="white" />
            </TouchableOpacity>
          </View>
        </SafeAreaView> */}

        {/* --- BOTTOM INFO CARD --- */}
        <View style={styles.bottomCardContainer}>
          <View style={styles.bottomCard}>
            <View style={styles.dragHandle} />

            <View style={styles.infoRow}>
              <View style={styles.dangerIconBg}>
                <View style={styles.dangerIconDot} />
              </View>
              <View style={styles.textColumn}>
                <Text style={styles.dangerTag}>DANGER ZONE</Text>
                <Text style={styles.locationTitle}>Taal Main Crater</Text>
                <Text style={styles.locationSub}>2.4 km from your location · Avoid 7km radius</Text>
              </View>
            </View>

            {/* <TouchableOpacity style={styles.routeButton}>
              <Text style={styles.routeButtonText}>Get safe route</Text>
            </TouchableOpacity> */}
          </View>
        </View>
      </Animated.View>
      <BottomNavbar activeTab="map" />

    </View>

  );
};

const MapPin = ({ x, y, color, label, dotSize = 10 }: any) => (
  <View
    style={[
      styles.pinWrapper,
      {
        left: x as any,   
        top: y as any    
      }
    ]}
  >
    <View
      style={[
        styles.pinDot,
        {
          backgroundColor: color,
          width: dotSize,
          height: dotSize,
          borderRadius: dotSize / 2
        }
      ]}
    />
    <Text style={styles.pinLabel}>{label}</Text>
  </View>
);



export default MapScreen;