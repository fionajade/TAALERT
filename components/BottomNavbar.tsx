import { Feather } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useEffect, useRef } from 'react';
import {
  Animated,
  TouchableOpacity,
  View
} from 'react-native';
import { BottomNavStyles as styles } from '../constants/theme';

const BottomNavbar = ({ activeTab }: { activeTab: string }) => {
  const router = useRouter();
  const plusScale = useRef(new Animated.Value(1)).current;
  const handlePlusPress = () => {
    Animated.sequence([
      Animated.timing(plusScale, { toValue: 0.92, duration: 100, useNativeDriver: true }),
      Animated.spring(plusScale, { toValue: 1, friction: 8, tension: 50, useNativeDriver: true }),
    ]).start();

    setTimeout(() => {
      router.push('/report');
    }, 150);
  };

  return (
    <View style={styles.navBarContainer}>
      <View style={styles.navBar}>
        <NavButton
          icon="home"
          isActive={activeTab === 'home'}
          onPress={() => router.replace('/')}
        />
        <NavButton
          icon="map"
          isActive={activeTab === 'map'}
          onPress={() => router.replace('/map')}
        />
        <View style={styles.plusBtnContainer}>
          <View style={styles.glowRing} />
          <Animated.View style={{ transform: [{ scale: plusScale }] }}>
            <TouchableOpacity
              style={styles.plusBtn}
              activeOpacity={0.9}
              onPress={handlePlusPress}
            >
              <Feather name="plus" size={28} color="white" />
            </TouchableOpacity>
          </Animated.View>
        </View>
        <NavButton
          icon="book-open"
          isActive={activeTab === 'resources'}
          onPress={() => router.replace('/resources')}
        />
        <NavButton
          icon="user"
          isActive={activeTab === 'profile'}
          onPress={() => router.replace('/profile')}
        />
      </View>
    </View>
  );
};

const NavButton = ({ icon, isActive, onPress }: any) => {
  const scaleAnim = useRef(new Animated.Value(1)).current;
  const dotOpacity = useRef(new Animated.Value(0)).current;
  const dotTranslateY = useRef(new Animated.Value(15)).current;

  useEffect(() => {
    if (isActive) {
      const timer = setTimeout(() => {
        Animated.parallel([
          Animated.spring(scaleAnim, {
            toValue: 1.25,
            useNativeDriver: true,
            friction: 4,
            tension: 40,
          }),
          Animated.timing(dotOpacity, {
            toValue: 1,
            duration: 300,
            useNativeDriver: true,
          }),
          Animated.spring(dotTranslateY, {
            toValue: 0,
            useNativeDriver: true,
            friction: 5,
          })
        ]).start();
      }, 100);

      return () => clearTimeout(timer);
    } else {
      scaleAnim.setValue(1);
      dotOpacity.setValue(0);
      dotTranslateY.setValue(15);
    }
  }, [isActive]);

  return (
    <TouchableOpacity
      style={styles.navItem}
      onPress={onPress}
      activeOpacity={1}
    >
      <Animated.View style={{ transform: [{ scale: scaleAnim }] }}>
        <Feather
          name={icon}
          size={22}
          color={isActive ? "#0096FF" : "#64748B"}
        />
      </Animated.View>

      <Animated.View
        style={[
          styles.activeDot,
          {
            opacity: dotOpacity,
            transform: [{ translateY: dotTranslateY }]
          }
        ]}
      />
    </TouchableOpacity>
  );
};


export default BottomNavbar;