import React, { useCallback, useEffect, useRef, useState } from 'react';
import carousel from './carousel.module.css';
import Image from "next/image";

export interface Slide {
    id: number;
    title: string;
    path: string;
    logoPath: string;
    text: string;
    description: string;
    readMoreUrl: string;
}

interface Props {
    slides: Slide[];
}

const Carousel: React.FunctionComponent<Props> = ({ slides }) => {
    const touchStartDataRef =
        useRef<{
            clientX: number;
            clientY: number;
        } | null>(null);
    const [slideIndex, setSlideIndex] = useState<number>(0);
    const currentSlide: Slide = slides[slideIndex];
    const totalSlides = slides.length;
    // const slideProgress = (100 / totalSlides) * (slideIndex + 1);
    const lastSlideIndex = totalSlides - 1;
    const onSetSlide = useCallback(
        (slideIndex: number) => {
            setSlideIndex(slideIndex);
        },
        [slides],
    );
    const onSlideChange = useCallback(
        (index: number) => {
            let current = slideIndex;
            let next = (current += index);

            if (next === -1) {
                next = 2;
            }

            if (next === totalSlides) {
                next = 0;
            }

            onSetSlide(next);
        },
        [slideIndex, totalSlides],
    );
    const onTouchStart = useCallback(
        (event: React.TouchEvent<HTMLDivElement>) => {
            const { touches } = event as React.TouchEvent;
            const touchData: React.Touch = touches && touches[0];

            if (touchData) {
                touchStartDataRef.current = {
                    clientX: touchData.clientX,
                    clientY: touchData.clientY,
                };
            }
        },
        [],
    );
    const onTouchEnd = useCallback(
        (event: React.TouchEvent<HTMLDivElement>) => {
            const touches: React.TouchList = event.changedTouches;
            const touchEndData: React.Touch = touches && touches[0];
            const { current: touchStartData } = touchStartDataRef;

            if (!touchEndData || !touchStartData) {
                return;
            }

            touchStartDataRef.current = null;

            const touchEndX: number = touchEndData.clientX;
            const touchEndY: number = touchEndData.clientY;
            const xDiff: number = touchStartData.clientX - touchEndX;
            const yDiff: number = touchStartData.clientY - touchEndY;
            const absXDiff: number = Math.abs(xDiff);
            const isHorizontalSwipe: boolean = absXDiff > Math.abs(yDiff);

            if (isHorizontalSwipe && absXDiff > 40) {
                const nextSlideIndex: number =
                    slideIndex + (xDiff > 0 ? 1 : -1);
                let next = 0;
                if (nextSlideIndex >= 0 && nextSlideIndex <= lastSlideIndex) {
                    next = nextSlideIndex;
                }
                onSetSlide(next);
            }
        },
        [lastSlideIndex, slideIndex, onSetSlide],
    );

    useEffect(() => {
        setSlideIndex(0);
    }, []);

    let offsetX = 0;

    return (
        <div
            className={carousel.layout}
            onTouchStart={onTouchStart}
            onTouchEnd={onTouchEnd}
        >
            <div className={carousel.slide} key={`slide${slideIndex}`}>
                <div className={carousel.slideContainer}>
                    <div className={carousel.sliderImg}>
                        <Image layout="fill"
                            src={currentSlide.path}
                            width={248}
                            height={276}
                            alt=""
                        />
                    </div>
                    <div className={carousel.slideContent}>
                        <div className={carousel.slideText}>
                            <div>
                                {currentSlide.text}
                                &nbsp;
                                <br className={carousel.mobile} />
                                <div>
                                    {' '}
                                    <a
                                        className={carousel.readMore}
                                        target="_blank"
                                        rel="noreferrer"
                                        download={currentSlide.readMoreUrl}
                                        href={currentSlide.readMoreUrl}
                                    >
                                        Read more
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div className={carousel.slideDescContainer}>
                            <div>
                                <Image layout="fill"
                                    width={64}
                                    height={64}
                                    src={currentSlide.logoPath}
                                    alt={currentSlide.description}
                                />
                            </div>
                            <div>
                                <div className={`${carousel.slideTitle}`}>
                                    {currentSlide.title}
                                </div>
                                <div className={`${carousel.slideDescription}`}>
                                    {currentSlide.description}
                                </div>
                            </div>
                        </div>
                        <div className={carousel.sliderCount}>
                            {slideIndex + 1}&nbsp;of&nbsp;{totalSlides}
                        </div>
                    </div>
                </div>
                {totalSlides !== 1 && (
                    <div className={carousel.sliderNavigation}>
                        <div
                            onClick={onSlideChange.bind(null, -1)}
                            className={carousel.sliderNavigationBtn}
                        >
                            <Image layout="fill"
                                src="/arrow-left.svg"
                                width={30}
                                height={22}
                                alt=""
                            />
                        </div>
                        <div
                            onClick={onSlideChange.bind(null, 1)}
                            className={carousel.sliderNavigationBtn}
                        >
                            <Image layout="fill"
                                src="/arrow-right.svg"
                                width={30}
                                height={22}
                                alt=""
                            />
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default React.memo(Carousel);
